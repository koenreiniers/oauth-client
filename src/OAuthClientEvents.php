<?php
namespace Kr\OAuthClient;

class OAuthClientEvents
{
    // TODO: MOVE
    const SERVER_REQUEST = "client.server_request";

    /**
     * Dispatched right before a resource request is sent
     *
     * Used to add authentication to the request
     */
    const RESOURCE_REQUEST = "oauth.client.resource_request";

    /**
     * Dispatched after the server has responded to a resource request
     *
     * Just there for convenience
     */
    const RESOURCE_RESPONSE = "oauth.client.resource_response";

    /**
     * Dispatched right before the client requests a new access token
     * At this point the grant_type will have already been resolved
     *
     * Used to add correct credentials to the token request
     */
    const TOKEN_REQUEST = "oauth.client.token_request";

    /**
     * Dispatched right after the server has responded to a token request
     *
     * Used to store access tokens
     */
    const TOKEN_RESPONSE = "oauth.client.token_response";

    /**
     * Dispatched right before the user is redirected to the authorization url
     *
     * Used to build the authorization url
     */
    const AUTHORIZATION_REQUEST = "oauth.client.authorization_request";

    /**
     * Dispatched whenever a user has just completed the authorization process
     * And has been redirected back to the redirect_uri
     *
     * Used for either the authorization code grant or the implicit grant
     */
    const AUTHORIZATION_RESPONSE = "oauth.client.authorization_response";
}