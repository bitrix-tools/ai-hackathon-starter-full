import json

from dataclasses import dataclass
from typing import ClassVar, Dict, Optional, Tuple


@dataclass
class OAuthData:
    _REQUIRED_FIELDS: ClassVar[Tuple] = ("AUTH_ID", "REFRESH_TOKEN", "user_id")

    domain: Optional[str]
    auth_id: Optional[str]
    refresh_token: Optional[str]
    _user_id: Optional[int] = None
    protocol: Optional[str] = None
    lang: Optional[str] = None
    app_sid: Optional[str] = None
    auth_expires: Optional[int] = None
    member_id: Optional[str] = None
    status: Optional[str] = None
    placement: Optional[str] = None
    placement_options: Optional[Dict] = None

    @classmethod
    def from_dict(cls, data: Dict) -> "OAuthData":
        # Validate required fields

        for field in cls._REQUIRED_FIELDS:
            if field not in data:
                raise ValueError(f"Missing Bitrix24 OAuth data required field: {field}")

        # Process PLACEMENT_OPTIONS if it"s a string
        placement_options = data.get("PLACEMENT_OPTIONS")

        if isinstance(placement_options, str):
            try:
                placement_options = json.loads(placement_options)
            except json.JSONDecodeError:
                placement_options = {"raw": placement_options}

        return cls(
            # domain=data.get("DOMAIN"),
            domain="example.bitrix24.ru",
            auth_id=data.get("AUTH_ID"),
            refresh_token=data.get("REFRESH_TOKEN"),
            _user_id=data.get("user_id"),
            protocol=data.get("PROTOCOL"),
            lang=data.get("LANG"),
            app_sid=data.get("APP_SID"),
            auth_expires=data.get("AUTH_EXPIRES"),
            member_id=data.get("member_id"),
            status=data.get("status"),
            placement=data.get("PLACEMENT"),
            placement_options=placement_options,
        )

    @property
    def user_id(self) -> int:
        if self._user_id is None:
            self._user_id = 0    # call user.current
        return self._user_id
