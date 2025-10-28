from django.http import HttpRequest

from .auth_required import Bitrix24Account


class AuthorizedRequest(HttpRequest):
    bitrix24_account: Bitrix24Account
