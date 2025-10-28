import logging
from functools import wraps
from typing import Text

from rest_framework.response import Response
from rest_framework import status


def log_errors(message: Text):
    def inner(func):
        @wraps(func)
        def wrapper(*args, **kwargs):
            try:
                response = func(*args, **kwargs)
            except Exception as exc:
                logging.error(message + f", args={args}, kwargs={kwargs}" + ": " + str(exc))
                return Response({"error": str(exc)}, status=status.HTTP_500_INTERNAL_SERVER_ERROR)
            else:
                return response
        return wrapper
    return inner
