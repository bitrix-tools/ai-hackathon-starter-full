<?php

namespace App;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Exception\AMQPTimeoutException;

class RabbitMQClient {
  private $connection;
  private $channel;
  private $connected = false;

  public function __construct() {
    $this->connect();
  }

  private function connect() {
    try {
      $host = getenv('RABBITMQ_HOST') ?: 'rabbitmq';
      $port = getenv('RABBITMQ_PORT') ?: 5672;
      $user = getenv('RABBITMQ_USER') ?: 'guest';
      $password = getenv('RABBITMQ_PASS') ?: 'guest';
      $vhost = getenv('RABBITMQ_VHOST') ?: '/';

      $this->connection = new AMQPStreamConnection(
        $host,
        $port,
        $user,
        $password,
        $vhost,
        false, // insist
        'AMQPLAIN', // login_method
        null, // login_response
        'en_US', // locale
        3.0, // connection_timeout
        3.0, // read_write_timeout
        null, // context
        false, // keepalive
        60 // heartbeat
      );

      $this->channel = $this->connection->channel();
      $this->connected = true;

      error_log("RabbitMQ connected successfully to {$host}:{$port}");

    } catch (Exception $e) {
      error_log("RabbitMQ connection failed: " . $e->getMessage());
      $this->connected = false;
      throw $e;
    }
  }

  private function ensureConnection() {
    if (!$this->connected || !$this->channel->is_open()) {
      $this->connect();
    }
  }

  /**
   * Posting a message to a queue
   */
  public function publish($queue, $data, $options = []) {
    $this->ensureConnection();

    try {
      // We declare a queue (stable)
      $this->channel->queue_declare(
        $queue,
        false, // passive
        true,  // durable - save messages across restarts
        false, // exclusive
        false  // auto_delete
      );

      $messageData = json_encode($data, JSON_UNESCAPED_UNICODE);

      $message = new AMQPMessage($messageData, [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'content_type' => 'application/json',
        'timestamp' => time(),
        'message_id' => uniqid('', true)
      ]);

      $this->channel->basic_publish($message, '', $queue);

      error_log("Message published to queue '{$queue}': " . $messageData);

      return true;

    } catch (Exception $e) {
      error_log("Failed to publish message to queue '{$queue}': " . $e->getMessage());
      $this->connected = false;
      throw $e;
    }
  }

  /**
   * Publication in exchange
   */
  public function publishToExchange($exchange, $routingKey, $data, $exchangeType = AMQPExchangeType::DIRECT) {
    $this->ensureConnection();

    try {
      // We announce an exchange
      $this->channel->exchange_declare(
        $exchange,
        $exchangeType,
        false, // passive
        true,  // durable
        false  // auto_delete
      );

      $messageData = json_encode($data, JSON_UNESCAPED_UNICODE);

      $message = new AMQPMessage($messageData, [
        'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        'content_type' => 'application/json'
      ]);

      $this->channel->basic_publish($message, $exchange, $routingKey);

      error_log("Message published to exchange '{$exchange}' with routing key '{$routingKey}': " . $messageData);

      return true;

    } catch (Exception $e) {
      error_log("Failed to publish message to exchange '{$exchange}': " . $e->getMessage());
      $this->connected = false;
      throw $e;
    }
  }

  /**
   * Subscribing to a queue with a callback
   */
  public function consume($queue, $callback, $options = []) {
    $this->ensureConnection();

    try {
      // We announce the queue
      $this->channel->queue_declare(
        $queue,
        false, // passive
        true,  // durable
        false, // exclusive
        false  // auto_delete
      );

      // QoS - prefetch count
      $prefetchCount = $options['prefetch_count'] ?? 1;
      $this->channel->basic_qos(null, $prefetchCount, null);

      $consumerTag = $options['consumer_tag'] ?? '';

      $this->channel->basic_consume(
        $queue,
        $consumerTag,
        false, // no_local
        false, // no_ack
        false, // exclusive
        false, // nowait
        function ($message) use ($callback, $queue) {
          try {
            $data = json_decode($message->body, true);
            $result = $callback($data, $message);

            if ($result !== false) {
              $message->ack();
              error_log("Message processed successfully from queue '{$queue}'");
            } else {
              $message->nack(true); // requeue
              error_log("Message processing failed, requeued from queue '{$queue}'");
            }
          } catch (Exception $e) {
            error_log("Error processing message from queue '{$queue}': " . $e->getMessage());
            $message->nack(false); // don't requeue
          }
        }
      );

      error_log("Started consuming queue '{$queue}'");

      // Infinite message processing loop
      while ($this->channel->is_consuming()) {
        try {
          $this->channel->wait(null, false, 5); // timeout 5 seconds
        } catch (AMQPTimeoutException $e) {
          // Timeout - checking connection
          if (!$this->channel->is_open()) {
            error_log("Channel closed, reconnecting...");
            $this->connect();
            // We're resubscribing
            $this->consume($queue, $callback, $options);
          }
          continue;
        } catch (Exception $e) {
          error_log("Error in consumer wait: " . $e->getMessage());
          sleep(5); // Wait before reconnecting
          $this->connect();
          $this->consume($queue, $callback, $options);
        }
      }

    } catch (Exception $e) {
      error_log("Failed to consume queue '{$queue}': " . $e->getMessage());
      throw $e;
    }
  }

  /**
   * Receive one message (without subscription)
   */
  public function get($queue, $ack = true) {
    $this->ensureConnection();

    try {
      $message = $this->channel->basic_get($queue, !$ack);

      if ($message) {
        $data = json_decode($message->body, true);

        if ($ack) {
          $message->ack();
        }

        return $data;
      }

      return null;

    } catch (Exception $e) {
      error_log("Failed to get message from queue '{$queue}': " . $e->getMessage());
      throw $e;
    }
  }

  /**
   * Declare an exchange and bind a queue
   */
  public function bindQueue($queue, $exchange, $routingKey = '') {
    $this->ensureConnection();

    try {
      $this->channel->queue_declare($queue, false, true, false, false);
      $this->channel->queue_bind($queue, $exchange, $routingKey);

      return true;

    } catch (Exception $e) {
      error_log("Failed to bind queue '{$queue}' to exchange '{$exchange}': " . $e->getMessage());
      throw $e;
    }
  }

  /**
   * Check connection status
   */
  public function isConnected() {
    return $this->connected && $this->channel && $this->channel->is_open();
  }

  /**
   * Close the connection
   */
  public function close() {
    try {
      if ($this->channel && $this->channel->is_open()) {
        $this->channel->close();
      }
      if ($this->connection) {
        $this->connection->close();
      }
      $this->connected = false;
      error_log("RabbitMQ connection closed");
    } catch (Exception $e) {
      error_log("Error closing RabbitMQ connection: " . $e->getMessage());
    }
  }

  public function __destruct() {
    $this->close();
  }
}
