<?php
namespace StravaDL\Exception;

use Guzzle\Http\Exception\ClientErrorResponseException;

class UnauthorizedException extends ClientErrorResponseException{}