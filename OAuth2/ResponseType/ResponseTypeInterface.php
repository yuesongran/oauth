<?php

namespace OAuth2\ResponseType;

interface ResponseTypeInterface
{
    /**
     * @param array $params
     * @param mixed $openID
     * @return mixed
     */
    public function getAuthorizeResponse($params, $openID = null);
}
