from django.views.decorators.csrf import csrf_exempt
from django.utils import timezone

from rest_framework.decorators import api_view
from rest_framework.response import Response

from .utils import AuthorizedRequest, auth_required, log_errors

__all__ = [
    "root",
    "health",
    "get_enum",
    "get_list",
    "install",
    "get_token",
]


@api_view(["GET"])
@log_errors("root")
@auth_required
def root(request: AuthorizedRequest):
    return Response({"message": "Python Backend is running"})


@api_view(["GET"])
@log_errors("health")
@auth_required
def health(request: AuthorizedRequest):
    return Response({
        "status": "healthy",
        "backend": "python",
        "timestamp": timezone.now().timestamp(),
    })


@api_view(["GET"])
@log_errors("get_enum")
@auth_required
def get_enum(request: AuthorizedRequest):
    options = ["option 1", "option 2", "option 3"]
    return Response(options)


@api_view(["GET"])
@log_errors("get_list")
@auth_required
def get_list(request: AuthorizedRequest):
    elements = ["element 1", "element 2", "element 3"]
    return Response(elements)


@csrf_exempt
@api_view(["POST"])
@log_errors("install")
@auth_required
def install(request: AuthorizedRequest):
    # Installation code
    ...
    return Response({"message": "Installation successful"})


@csrf_exempt
@api_view(["POST"])
@log_errors("get_token")
@auth_required
def get_token(request: AuthorizedRequest):
    return Response({"token": request.bitrix24_account.create_jwt_token()})
