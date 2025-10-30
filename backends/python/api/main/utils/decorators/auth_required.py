from functools import wraps
from http import HTTPStatus
from typing import cast

import jwt

from django.http import JsonResponse, HttpRequest

from b24pysdk.bitrix_api.credentials import OAuthPlacementData
from b24pysdk.error import BitrixValidationError
from b24pysdk.utils.types import JSONDict

from ...models import Bitrix24Account
from .collect_request_data import collect_request_data


def auth_required(view_func):
    @wraps(view_func)
    @collect_request_data
    def wrapped(request: HttpRequest, *args, **kwargs):
        auth = request.headers.get("Authorization")

        if isinstance(auth, str) and auth.lower().startswith("bearer "):
            jwt_token = auth[len("bearer "):]

            try:
                request.bitrix24_account = Bitrix24Account.get_from_jwt_token(jwt_token)

            except Bitrix24Account.DoesNotExist:
                return JsonResponse({"error": "Invalid JWT token"}, status=HTTPStatus.UNAUTHORIZED)

            except jwt.ExpiredSignatureError:
                return JsonResponse({"error": "JWT token has expired"}, status=HTTPStatus.UNAUTHORIZED)

            except jwt.InvalidTokenError:
                return JsonResponse({"error": "Invalid JWT token"}, status=HTTPStatus.UNAUTHORIZED)

            except BitrixValidationError as error:
                return JsonResponse({"error": str(error)}, status=HTTPStatus.BAD_REQUEST)

        else:
            # Create OAuthData and pass it in the request
            try:
                oauth_placement_data = OAuthPlacementData.from_dict(cast(JSONDict, request.data))
                request.bitrix24_account, _ = Bitrix24Account.update_or_create_from_oauth_placement_data(oauth_placement_data)

            except BitrixValidationError as error:
                return JsonResponse({"error": str(error)}, status=HTTPStatus.BAD_REQUEST)

        return view_func(request, *args, **kwargs)

    return wrapped
