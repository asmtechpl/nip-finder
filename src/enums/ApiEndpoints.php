<?php

namespace NipFinder\enums;

abstract class ApiEndpoints
{
    public const CHECK_USER_ENDPOINT = '/api/v1/status-service/for-user';

    public const REGISTER_ENDPOINT = '/api/v1/register';

    public const GENERATE_REGISTER_KEY_ENDPOINT = '/api/generate-register-key';

    public const REGISTER_ENDPOINT_LICENCE_ENDPOINT = '/register';
    public const REGISTER_SUBSCRIPTION_ENDPOINT = '/api/v1/subscription/free-register';

}
