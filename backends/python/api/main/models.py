from datetime import timedelta
from typing import TYPE_CHECKING, Tuple

import jwt
import uuid
from django.db import models
from django.utils import timezone

from config import config

if TYPE_CHECKING:
    from .utils import OAuthData


class Bitrix24Account(models.Model):
    id = models.UUIDField(primary_key=True, default=uuid.uuid4, editable=False)
    b24_user_id = models.IntegerField()
    is_b24_user_admin = models.BooleanField(default=False)
    member_id = models.CharField(max_length=255)
    is_master_account = models.BooleanField(null=True)
    domain_url = models.CharField(max_length=255, default="example.bitrix24.ru")
    status = models.CharField(max_length=50, default="P")
    application_token = models.CharField(max_length=255, null=True)
    created_at_utc = models.DateTimeField(auto_now_add=True)
    updated_at_utc = models.DateTimeField(auto_now=True)
    application_version = models.IntegerField()
    comment = models.TextField(null=True)
    access_token = models.CharField(max_length=255, null=True)
    refresh_token = models.CharField(max_length=255, null=True)
    expires = models.IntegerField(null=True)
    expires_in = models.IntegerField(null=True)
    current_scope = models.JSONField(null=True)

    class Meta:
        managed = False
        db_table = "bitrix24account"
        unique_together = ("b24_user_id", "domain_url")

    def create_jwt_token(self) -> str:
        now_dt = timezone.now()

        payload = {
            "account_id": self.pk,
            "exp": now_dt + timedelta(hours=1),
            "iat": now_dt,
        }

        return jwt.encode(payload, config.jwt_secret, algorithm=config.jwt_algorithm)

    @staticmethod
    def _validate_jwt_token(jwt_token: str) -> str:
        payload = jwt.decode(jwt_token, config.jwt_secret, algorithms=[config.jwt_algorithm])

        # Validate required fields in payload
        for key in ("account_id", "exp", "iat"):
            if key not in payload:
                raise ValueError("Invalid JWT token")

        return payload["account_id"]

    @classmethod
    def get_from_jwt_token(cls, jwt_token: str) -> "Bitrix24Account":
        return cls.objects.get(pk=cls._validate_jwt_token(jwt_token))

    @classmethod
    def update_or_create_from_auth_data(cls, oauth_data: "OAuthData") -> Tuple["Bitrix24Account", bool]:
        """Create or update Bitrix24Account"""

        defaults = {
            "member_id": oauth_data.member_id,
            "status": oauth_data.status,
            "access_token": oauth_data.auth_id,
            "refresh_token": oauth_data.refresh_token,
            # "expires": oauth_data.auth_expires,
            "application_version": 1,
        }

        bitrix24_account, is_created = cls.objects.update_or_create(
            domain_url=oauth_data.domain,
            b24_user_id=oauth_data.user_id,
            defaults=defaults,
        )

        return bitrix24_account, is_created


class ApplicationInstallation(models.Model):
    id = models.UUIDField(primary_key=True, editable=False)
    status = models.CharField(max_length=50)
    created_at_utc = models.DateTimeField(auto_now_add=True)
    update_at_utc = models.DateTimeField(auto_now=True)
    bitrix_24_account = models.OneToOneField(Bitrix24Account, on_delete=models.CASCADE, db_column="bitrix_24_account_id")
    contact_person_id = models.UUIDField(null=True)
    bitrix_24_partner_contact_person_id = models.UUIDField(null=True)
    bitrix_24_partner_id = models.UUIDField(null=True)
    external_id = models.CharField(max_length=255, null=True)
    portal_license_family = models.CharField(max_length=255)
    portal_users_count = models.IntegerField(null=True)
    application_token = models.CharField(max_length=255, null=True)
    comment = models.TextField(null=True)
    status_code = models.JSONField(null=True)

    class Meta:
        managed = False
        db_table = "application_installation"
