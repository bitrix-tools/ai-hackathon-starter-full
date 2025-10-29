import logging
from functools import wraps
from http import HTTPStatus

from django.http import JsonResponse


def log_errors(message: str):
    def inner(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            try:
                response = func(*args, **kwargs)
            except Exception as exc:
                logging.error(message + f", args={args}, kwargs={kwargs}" + ": " + str(exc))
                return JsonResponse({"error": str(exc)}, status=HTTPStatus.INTERNAL_SERVER_ERROR)
            else:
                return response
        return wrapper
    return inner
