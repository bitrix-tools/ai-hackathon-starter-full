from typing import TYPE_CHECKING

from django.http import HttpRequest

if TYPE_CHECKING:
    from ..models import Bitrix24Account


class AuthorizedRequest(HttpRequest):
    bitrix24_account: "Bitrix24Account"
