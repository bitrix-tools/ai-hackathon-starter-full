const express = require('express');
const cors = require('cors');
const { Pool } = require('pg');

const app = express();
app.use(cors());
app.use(express.json());

const pool = new Pool({
  host: process.env.DB_HOST || 'database',
  port: process.env.DB_PORT || 5432,
  database: process.env.DB_NAME || 'appdb',
  user: process.env.DB_USER || 'appuser',
  password: process.env.DB_PASSWORD || 'apppass'
});

app.get('/api/health', (req, res) => {
  res.json({
    status: 'healthy',
    backend: 'node',
    timestamp: Math.floor(Date.now() / 1000)
  });
});

app.get('/api/enum', async (req, res) => {
  res.json([
    'option 1',
    'option 2',
    'option 3'
  ]);
});

app.get('/api/list', async (req, res) => {
  res.json([
    'element 1',
    'element 2',
    'element 3'
  ]);
});

app.post('/api/install', async (req, res) => {
  req.body
  res.json({
    message: 'All success'
  });
});

const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
