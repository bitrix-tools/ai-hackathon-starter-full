from functools import wraps

import jwt

from rest_framework.response import Response
from rest_framework import status

from ..models import Bitrix24Account

from .oauth_data import OAuthData


def auth_required(view_func):
    @wraps(view_func)
    def wrapped(request, *args, **kwargs):
        auth = request.headers.get("Authorization")

        if isinstance(auth, str) and auth.lower().startswith("bearer "):
            jwt_token = auth[len("bearer "):]

            try:
                request.bitrix24_account = Bitrix24Account.get_from_jwt_token(jwt_token)

            except Bitrix24Account.DoesNotExist:
                return Response({"error": "Invalid JWT token"}, status=status.HTTP_401_UNAUTHORIZED)

            except jwt.ExpiredSignatureError:
                return Response({"error": "JWT token has expired"}, status=status.HTTP_401_UNAUTHORIZED)

            except jwt.InvalidTokenError:
                return Response({"error": "Invalid JWT token"}, status=status.HTTP_401_UNAUTHORIZED)

            except ValueError as error:
                return Response({"error": str(error)}, status=status.HTTP_400_BAD_REQUEST)

        else:
            # Create OAuthData and pass it in the request
            try:
                oauth_data = OAuthData.from_dict(request.data)
                request.bitrix24_account, _ = Bitrix24Account.update_or_create_from_auth_data(oauth_data)

            except ValueError as error:
                return Response({"error": str(error)}, status=status.HTTP_400_BAD_REQUEST)

        return view_func(request, *args, **kwargs)

    return wrapped
