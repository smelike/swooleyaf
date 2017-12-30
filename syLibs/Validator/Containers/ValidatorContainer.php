<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017-3-26
 * Time: 0:53
 */
namespace Validator\Containers;

use Constant\Server;
use Tool\BaseContainer;
use Validator\Impl\Double\DoubleBetween;
use Validator\Impl\Double\DoubleMax;
use Validator\Impl\Double\DoubleMin;
use Validator\Impl\Double\DoubleRequired;
use Validator\Impl\Int\IntBetween;
use Validator\Impl\Int\IntIn;
use Validator\Impl\Int\IntMax;
use Validator\Impl\Int\IntMin;
use Validator\Impl\Int\IntRequired;
use Validator\Impl\String\StringBaseImage;
use Validator\Impl\String\StringEmail;
use Validator\Impl\String\StringIP;
use Validator\Impl\String\StringJson;
use Validator\Impl\String\StringLat;
use Validator\Impl\String\StringLng;
use Validator\Impl\String\StringMax;
use Validator\Impl\String\StringMin;
use Validator\Impl\String\StringNoEmoji;
use Validator\Impl\String\StringNoJs;
use Validator\Impl\String\StringPhone;
use Validator\Impl\String\StringRegex;
use Validator\Impl\String\StringRequired;
use Validator\Impl\String\StringSign;
use Validator\Impl\String\StringTel;
use Validator\Impl\String\StringUrl;
use Validator\Impl\String\StringZh;

class ValidatorContainer extends BaseContainer {
    public function __construct() {
        $this->registryMap = [
            Server::VALIDATOR_INT_TYPE_REQUIRED,
            Server::VALIDATOR_INT_TYPE_MIN,
            Server::VALIDATOR_INT_TYPE_MAX,
            Server::VALIDATOR_INT_TYPE_IN,
            Server::VALIDATOR_INT_TYPE_BETWEEN,
            Server::VALIDATOR_DOUBLE_TYPE_REQUIRED,
            Server::VALIDATOR_DOUBLE_TYPE_BETWEEN,
            Server::VALIDATOR_DOUBLE_TYPE_MIN,
            Server::VALIDATOR_DOUBLE_TYPE_MAX,
            Server::VALIDATOR_STRING_TYPE_REQUIRED,
            Server::VALIDATOR_STRING_TYPE_MIN,
            Server::VALIDATOR_STRING_TYPE_MAX,
            Server::VALIDATOR_STRING_TYPE_REGEX,
            Server::VALIDATOR_STRING_TYPE_PHONE,
            Server::VALIDATOR_STRING_TYPE_TEL,
            Server::VALIDATOR_STRING_TYPE_EMAIL,
            Server::VALIDATOR_STRING_TYPE_URL,
            Server::VALIDATOR_STRING_TYPE_JSON,
            Server::VALIDATOR_STRING_TYPE_SIGN,
            Server::VALIDATOR_STRING_TYPE_BASE_IMAGE,
            Server::VALIDATOR_STRING_TYPE_IP,
            Server::VALIDATOR_STRING_TYPE_LNG,
            Server::VALIDATOR_STRING_TYPE_LAT,
            Server::VALIDATOR_STRING_TYPE_NO_JS,
            Server::VALIDATOR_STRING_TYPE_NO_EMOJI,
            Server::VALIDATOR_STRING_TYPE_ZH,
        ];

        $this->bind(Server::VALIDATOR_INT_TYPE_REQUIRED, function () {
            return new IntRequired();
        });

        $this->bind(Server::VALIDATOR_INT_TYPE_MIN, function () {
            return new IntMin();
        });

        $this->bind(Server::VALIDATOR_INT_TYPE_MAX, function () {
            return new IntMax();
        });

        $this->bind(Server::VALIDATOR_INT_TYPE_IN, function () {
            return new IntIn();
        });

        $this->bind(Server::VALIDATOR_INT_TYPE_BETWEEN, function () {
            return new IntBetween();
        });

        $this->bind(Server::VALIDATOR_DOUBLE_TYPE_REQUIRED, function () {
            return new DoubleRequired();
        });

        $this->bind(Server::VALIDATOR_DOUBLE_TYPE_BETWEEN, function () {
            return new DoubleBetween();
        });

        $this->bind(Server::VALIDATOR_DOUBLE_TYPE_MIN, function () {
            return new DoubleMin();
        });

        $this->bind(Server::VALIDATOR_DOUBLE_TYPE_MAX, function () {
            return new DoubleMax();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_REQUIRED, function () {
            return new StringRequired();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_MIN, function () {
            return new StringMin();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_MAX, function () {
            return new StringMax();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_REGEX, function () {
            return new StringRegex();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_PHONE, function () {
            return new StringPhone();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_TEL, function () {
            return new StringTel();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_EMAIL, function () {
            return new StringEmail();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_URL, function () {
            return new StringUrl();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_JSON, function () {
            return new StringJson();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_SIGN, function () {
            return new StringSign();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_BASE_IMAGE, function () {
            return new StringBaseImage();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_IP, function () {
            return new StringIP();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_LNG, function () {
            return new StringLng();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_LAT, function () {
            return new StringLat();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_NO_JS, function () {
            return new StringNoJs();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_NO_EMOJI, function () {
            return new StringNoEmoji();
        });

        $this->bind(Server::VALIDATOR_STRING_TYPE_ZH, function () {
            return new StringZh();
        });
    }
}