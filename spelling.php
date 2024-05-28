<?php

namespace Hp;

//  PROJECT HONEY POT ADDRESS DISTRIBUTION SCRIPT
//  For more information visit: http://www.projecthoneypot.org/
//  Copyright (C) 2004-2022, Unspam Technologies, Inc.
//
//  This program is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with this program; if not, write to the Free Software
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
//  02111-1307  USA
//
//  If you choose to modify or redistribute the software, you must
//  completely disconnect it from the Project Honey Pot Service, as
//  specified under the Terms of Service Use. These terms are available
//  here:
//
//  http://www.projecthoneypot.org/terms_of_service_use.php
//
//  The required modification to disconnect the software from the
//  Project Honey Pot Service is explained in the comments below. To find the
//  instructions, search for:  *** DISCONNECT INSTRUCTIONS ***
//
//  Generated On: Sat, 13 Aug 2022 06:23:14 -0400
//  For Domain: bearsampp.com
//
//

//  *** DISCONNECT INSTRUCTIONS ***
//
//  You are free to modify or redistribute this software. However, if
//  you do so you must disconnect it from the Project Honey Pot Service.
//  To do this, you must delete the lines of code below located between the
//  *** START CUT HERE *** and *** FINISH CUT HERE *** comments. Under the
//  Terms of Service Use that you agreed to before downloading this software,
//  you may not recreate the deleted lines or modify this software to access
//  or otherwise connect to any Project Honey Pot server.
//
//  *** START CUT HERE ***

define('__REQUEST_HOST', 'hpr4.projecthoneypot.org');
define('__REQUEST_PORT', '80');
define('__REQUEST_SCRIPT', '/cgi/serve.php');

//  *** FINISH CUT HERE ***

interface Response
{
    public function getBody();
    public function getLines(): array;
}

class TextResponse implements Response
{
    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    public function getBody()
    {
        return $this->content;
    }

    public function getLines(): array
    {
        return explode("\n", $this->content);
    }
}

interface HttpClient
{
    public function request(string $method, string $url, array $headers = [], array $data = []): Response;
}

class ScriptClient implements HttpClient
{
    private $proxy;
    private $credentials;

    public function __construct(string $settings)
    {
        $this->readSettings($settings);
    }

    private function getAuthorityComponent(string $authority = null, string $tag = null)
    {
        if(is_null($authority)){
            return null;
        }
        if(!is_null($tag)){
            $authority .= ":$tag";
        }
        return $authority;
    }

    private function readSettings(string $file)
    {
        if(!is_file($file) || !is_readable($file)){
            return;
        }

        $stmts = file($file);

        $settings = array_reduce($stmts, function($c, $stmt){
            list($key, $val) = \array_pad(array_map('trim', explode(':', $stmt)), 2, null);
            $c[$key] = $val;
            return $c;
        }, []);

        $this->proxy       = $this->getAuthorityComponent($settings['proxy_host'], $settings['proxy_port']);
        $this->credentials = $this->getAuthorityComponent($settings['proxy_user'], $settings['proxy_pass']);
    }

    public function request(string $method, string $uri, array $headers = [], array $data = []): Response
    {
        $options = [
            'http' => [
                'method' => strtoupper($method),
                'header' => $headers + [$this->credentials ? 'Proxy-Authorization: Basic ' . base64_encode($this->credentials) : null],
                'proxy' => $this->proxy,
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $body = file_get_contents($uri, false, $context);

        if($body === false){
            trigger_error(
                "Unable to contact the Server. Are outbound connections disabled? " .
                "(If a proxy is required for outbound traffic, you may configure " .
                "the honey pot to use a proxy. For instructions, visit " .
                "http://www.projecthoneypot.org/settings_help.php)",
                E_USER_ERROR
            );
        }

        return new TextResponse($body);
    }
}

trait AliasingTrait
{
    private $aliases = [];

    public function searchAliases($search, array $aliases, array $collector = [], $parent = null): array
    {
        foreach($aliases as $alias => $value){
            if(is_array($value)){
                return $this->searchAliases($search, $value, $collector, $alias);
            }
            if($search === $value){
                $collector[] = $parent ?? $alias;
            }
        }

        return $collector;
    }

    public function getAliases($search): array
    {
        $aliases = $this->searchAliases($search, $this->aliases);
    
        return !empty($aliases) ? $aliases : [$search];
    }

    public function aliasMatch($alias, $key)
    {
        return $key === $alias;
    }

    public function setAlias($key, $alias)
    {
        $this->aliases[$alias] = $key;
    }

    public function setAliases(array $array)
    {
        array_walk($array, function($v, $k){
            $this->aliases[$k] = $v;
        });
    }
}

abstract class Data
{
    protected $key;
    protected $value;

    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function key()
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }
}

class DataCollection
{
    use AliasingTrait;

    private $data;

    public function __construct(Data ...$data)
    {
        $this->data = $data;
    }

    public function set(Data ...$data)
    {
        array_map(function(Data $data){
            $index = $this->getIndexByKey($data->key());
            if(is_null($index)){
                $this->data[] = $data;
            } else {
                $this->data[$index] = $data;
            }
        }, $data);
    }

    public function getByKey($key)
    {
        $key = $this->getIndexByKey($key);
        return !is_null($key) ? $this->data[$key] : null;
    }

    public function getValueByKey($key)
    {
        $data = $this->getByKey($key);
        return !is_null($data) ? $data->value() : null;
    }

    private function getIndexByKey($key)
    {
        $result = [];
        array_walk($this->data, function(Data $data, $index) use ($key, &$result){
            if($data->key() == $key){
                $result[] = $index;
            }
        });

        return !empty($result) ? reset($result) : null;
    }
}

interface Transcriber
{
    public function transcribe(array $data): DataCollection;
    public function canTranscribe($value): bool;
}

class StringData extends Data
{
    public function __construct($key, string $value)
    {
        parent::__construct($key, $value);
    }
}

class CompressedData extends Data
{
    public function __construct($key, string $value)
    {
        parent::__construct($key, $value);
    }

    public function value()
    {
        $url_decoded = base64_decode(str_replace(['-','_'],['+','/'],$this->value));
        if(substr(bin2hex($url_decoded), 0, 6) === '1f8b08'){
            return gzdecode($url_decoded);
        } else {
            return $this->value;
        }
    }
}

class FlagData extends Data
{
    private $data;

    public function setData($data)
    {
        $this->data = $data;
    }

    public function value()
    {
        return $this->value ? ($this->data ?? null) : null;
    }
}

class CallbackData extends Data
{
    private $arguments = [];

    public function __construct($key, callable $value)
    {
        parent::__construct($key, $value);
    }

    public function setArgument($pos, $param)
    {
        $this->arguments[$pos] = $param;
    }

    public function value()
    {
        ksort($this->arguments);
        return \call_user_func_array($this->value, $this->arguments);
    }
}

class DataFactory
{
    private $data;
    private $callbacks;

    private function setData(array $data, string $class, DataCollection $dc = null)
    {
        $dc = $dc ?? new DataCollection;
        array_walk($data, function($value, $key) use($dc, $class){
            $dc->set(new $class($key, $value));
        });
        return $dc;
    }

    public function setStaticData(array $data)
    {
        $this->data = $this->setData($data, StringData::class, $this->data);
    }

    public function setCompressedData(array $data)
    {
        $this->data = $this->setData($data, CompressedData::class, $this->data);
    }

    public function setCallbackData(array $data)
    {
        $this->callbacks = $this->setData($data, CallbackData::class, $this->callbacks);
    }

    public function fromSourceKey($sourceKey, $key, $value)
    {
        $keys = $this->data->getAliases($key);
        $key = reset($keys);
        $data = $this->data->getValueByKey($key);

        switch($sourceKey){
            case 'directives':
                $flag = new FlagData($key, $value);
                if(!is_null($data)){
                    $flag->setData($data);
                }
                return $flag;
            case 'email':
            case 'emailmethod':
                $callback = $this->callbacks->getByKey($key);
                if(!is_null($callback)){
                    $pos = array_search($sourceKey, ['email', 'emailmethod']);
                    $callback->setArgument($pos, $value);
                    $this->callbacks->set($callback);
                    return $callback;
                }
            default:
                return new StringData($key, $value);
        }
    }
}

class DataTranscriber implements Transcriber
{
    private $template;
    private $data;
    private $factory;

    private $transcribingMode = false;

    public function __construct(DataCollection $data, DataFactory $factory)
    {
        $this->data = $data;
        $this->factory = $factory;
    }

    public function canTranscribe($value): bool
    {
        if($value == '<BEGIN>'){
            $this->transcribingMode = true;
            return false;
        }

        if($value == '<END>'){
            $this->transcribingMode = false;
        }

        return $this->transcribingMode;
    }

    public function transcribe(array $body): DataCollection
    {
        $data = $this->collectData($this->data, $body);

        return $data;
    }

    public function collectData(DataCollection $collector, array $array, $parents = []): DataCollection
    {
        foreach($array as $key => $value){
            if($this->canTranscribe($value)){
                $value = $this->parse($key, $value, $parents);
                $parents[] = $key;
                if(is_array($value)){
                    $this->collectData($collector, $value, $parents);
                } else {
                    $data = $this->factory->fromSourceKey($parents[1], $key, $value);
                    if(!is_null($data->value())){
                        $collector->set($data);
                    }
                }
                array_pop($parents);
            }
        }
        return $collector;
    }

    public function parse($key, $value, $parents = [])
    {
        if(is_string($value)){
            if(key($parents) !== NULL){
                $keys = $this->data->getAliases($key);
                if(count($keys) > 1 || $keys[0] !== $key){
                    return \array_fill_keys($keys, $value);
                }
            }

            end($parents);
            if(key($parents) === NULL && false !== strpos($value, '=')){
                list($key, $value) = explode('=', $value, 2);
                return [$key => urldecode($value)];
            }

            if($key === 'directives'){
                return explode(',', $value);
            }

        }

        return $value;
    }
}

interface Template
{
    public function render(DataCollection $data): string;
}

class ArrayTemplate implements Template
{
    public $template;

    public function __construct(array $template = [])
    {
        $this->template = $template;
    }

    public function render(DataCollection $data): string
    {
        $output = array_reduce($this->template, function($output, $key) use($data){
            $output[] = $data->getValueByKey($key) ?? null;
            return $output;
        }, []);
        ksort($output);
        return implode("\n", array_filter($output));
    }
}

class Script
{
    private $client;
    private $transcriber;
    private $template;
    private $templateData;
    private $factory;

    public function __construct(HttpClient $client, Transcriber $transcriber, Template $template, DataCollection $templateData, DataFactory $factory)
    {
        $this->client = $client;
        $this->transcriber = $transcriber;
        $this->template = $template;
        $this->templateData = $templateData;
        $this->factory = $factory;
    }

    public static function run(string $host, int $port, string $script, string $settings = '')
    {
        $client = new ScriptClient($settings);

        $templateData = new DataCollection;
        $templateData->setAliases([
            'doctype'   => 0,
            'head1'     => 1,
            'robots'    => 8,
            'nocollect' => 9,
            'head2'     => 1,
            'top'       => 2,
            'legal'     => 3,
            'style'     => 5,
            'vanity'    => 6,
            'bottom'    => 7,
            'emailCallback' => ['email','emailmethod'],
        ]);

        $factory = new DataFactory;
        $factory->setStaticData([
            'doctype' => '<!DOCTYPE html>',
            'head1'   => '<html><head>',
            'head2'   => '<title>Job Horror</title></head>',
            'top'     => '<body><div align="center">',
            'bottom'  => '</div></body></html>',
        ]);
        $factory->setCompressedData([
            'robots'    => 'H4sIAAAAAAAAA7PJTS1JVMhLzE21VSrKT8ovKVZSSM7PK0nNK7FVystPLErOyCxLVbKzwa8uMy8ltYKAqrT8nJz8ciU7AI6l-bpzAAAA',
            'nocollect' => 'H4sIAAAAAAAAA7PJTS1JVMhLzE21VcrL103NTczM0U3Oz8lJTS7JzM9TUkjOzytJzSuxVdJXsgMAKsBXli0AAAA',
            'legal'     => 'H4sIAAAAAAAAA7Vc227bSBJ9368wPItMBsgksqM4MeQx4CRKrMHEztpKgnmkSErijixqScpe79cvWafZdbrVpIUF1gAJiiL7UpdTp6pbPqui2So9iNPVahMlSbZe_HY4ODyY5UWSFnLZfFVuoth8df63s6poTsn5WVWdP1vPys3o4GxWnJ_N83V1EOervPjtp0_yd56fvWrunjffT5fNucqbc1415x9pcy7k_Oyn07ejBa6Ojk5Gy_ZyMMr1WbkpXZ7Ngh2mbYevZs7Dxflt09ixXF7lbeNvRpvmclM052jbdjG-wwNvX49koNGqOS-j5lzKnXtcY6zu-LXbA3cEY3knkoeeH4owZJal9ButRTBz25h8vk31uc6G_5T53KH5WXP-nrUvfJdZNFdfL4wY3r2Rz2evqkZOtSI9bXaIdu6JtjlSGsmjyKhV5rtRKcNJ5DGZSypSXf_anGdlYEZGK4OjUaxPZtJCvlDjkXazLpNLzDCzVsdHkGQpXeSi6dVWrwtrWB3zrmjeKvPmiJc6lN-3Kg_cT0g2otVHuVHc64OB-W8P9NGtCKm0uqxV93o0aa5upvh8cjK67dWlbfdBppnLKAu5m1VdIizNjCt1RzgLPg9P0GxVUDNhTb4ZRcaTjkcQEGQeqcGKPnpd2SovfWmdI_RCTC9ELZTUxnSgoyE7DI1YPgoirEX0SaYWgi8FCDZyIxXDjMRUs1h9L9BwLHYbbXgSjVStOU9rCZ3Cl-Wl8T_20yrcCDflskrVupY0bukJCBwBVXUsgQGv5OoOop4J0A2Ho1aOp2QOlbVOuDoLq7ZXTApomb4QExRfWBwoLgT6j8RMKniT9LDIrO4fpJ-5mhPGgMnk9x1NQqMIAjRGcaTCotGNuNP1TXP-Nt5PCfCArSi_ENt6BCDLE3PM9EkLn7fNBcYOoa4JUARh9aOIPrbqEtst1PLg9qtm8m_pragLADIGgKpROZRfdaFWJeCPMIVgiTDJoIv7-Vpf_1NtNdAmjAOWsIQn-0O3Hy_0UhR4Nd1PdUG1VKSWf2rHmAvwOY5ovrFaojuqjoCyCQRSiAthO1dWkYphrlZ2vPJ5sfYEARmuE_X6rPRcPSDgnQZIlpgOhH-Xek_jctb60xGgPQqxHxHTU8ww9H1kvqfAYwzf0JmFFfhn8dbxlT70fj_tz5vGYdmxyH-B6ahWC1W_EdFPp6eADwX7kMOKIksEHbkU7MtERI8WdUvo6j8ygr9Eh-Kvr3L7OjoPSWhhJPRDvL3Li5o2Xo9EViDdmGna3B_QxJI-HFDrKnIHgwDvJCUcT_JHl0eZnECgM0EAs6RsPMYsTt4Z5rMnKKM13ARjJwixSpwrJGbqKyW8joghefQtu21MFIkkI9pfO-8bBpOokgGmBXkeXFHsKv13hz7TtNXqEFPIqF9xXUMzCCNs5DMZSr4PDRJsSyXWritW-i6iCNhe1Z4J8V582E9F-Cg95JCFjGvTynTwDnQrUTfCK4X3Nhne19pSaqMkFUE-iPCwYfUVFQYZSjPDU_SMQYiYQQUTaQCMfEOI35uJ7Bp8SapfSyaY3LVdH9uua5uSjiqxi2WliNHNm4T9mBDQjB4Jb245-iVFSrm8PNhPWctMRyWmkflyA6zdy3MAOk0NjJQ-TFgzYGQvD9TeNb_z2l5aagMvVvSCryJ8giYZCtyVYFY0-l8oNVDDzlCWULyo_AAIh6i62DMapway1JtPc0zl-SshQde18wz-p6w8RCaQfz7JbRAarAiGTIvFNpKtGt7SUU09mIt6yCeOp1GmgQpLWnhyc40pyAt4gAsnl2g5kYmtot-fOS9trlbuYNQxCMIkspfKajNtJBgFeUirR6-dBTEix9x7tRYHtMbqUEmCooBG29mwF3_Ryy7Su4fpuPhEBZ05UtdYwWct9w3Lf0pe27BM_nBwADgo1R7wpNhSi_YAYsJ3yaBQkptt1ePxZRskj4_8cBNivwgBmBGKGpVDc5p4IM9t1bvzg92J2VvI_9aELjK2raaZdWoMax5LvnK5L6tBIicjjX1QwaXQ4OfIEwrvCXLfSR0o3zruG0kvl1t9R15P1TvIUus7GQldsDPRSSMjB7SjzLNRru1YwoVjCVCZXMrsUIdg542sxxo0qFpvblOmlTfTmjG6XlbToMK3JqreqVak3oaqab9-6l7qZ4cBNIR9qQkBtgCMuYYcBCKUgCnXpVkIr7niDhBpId18ps7THlwKosF8u1WTn8g5Q2K-pN6-ft2ditF07Zgi8hmFt_vW0E2msaTEh_pHWIq9mX243u3LZnnkPetMldYcU8kHPoPw2Td7MHB84wTJWJ2ZXORO-vr1sjk_YMXCrz2k3gRuHQOjpA--kJP1BooEIP1C_xbqI_ju7yyRNjPdYSOZLQZZW6kjs0O1tmBliOE9kL1Qk0m55iT2izZkpFXhyeBGMjTHl2nakpH8i8pPqtXmuPqMm8PBSFwOn2q13nQr84uTh5m6COT9VCZLotuinCmvIQ1u53s8wKhdZTUz5X4RJ1C2gfelDtEBI8UKAQT6ZtCcURSEiJBTMFc-_-CgYvWCjElc7QPqi22Uayk5A1qmXs6muhvrTaUHml958mmOF0YkQxNLv_SvOPiYTlUMFCMxSqRiQDxzP1eJ0ZDHjqKLn6XNjB3DUIXEQT-iaCuCIJHySyITmTpWQWV90_n7Pzog_QEqVYdd29xxgE7gRQZf5DZu-PB3_cnxGgLtR8sfTGxtjs8Sj6bXOp7rbie5-shtJ1Ql5LAQ6-V3nUofTAgDLEtvJlPHNQxjg1IjrzcrUV2fkwxP8zowuz9bDlxbEIJApF3LShRbSmCFjuoztKIMr4x1mBeJZyIxEUBnntbIv_uswqyKPUcYsQmrGvT0skuuSyNXCV6f-jPyBh4cdIAZ04I6vJ6ymoIWAcJlk4nT5ArLXjLvB5ADxUmr37YgUCus8GQApD9QQ4MLpr7F_OEgRXd4akuHuZpx1ZZjTX3mToaDysdaBJD6MWriIMnXdtBt4lvFrlVLBKrjEUool5ZndHLBxpkdhoYiPCHMjFYVobEVJS1Ym1X6wK7lRHNm_plOdZPxW6aQqlaAyDRHrT7xjTzYbT175AiuV62pfDUTLHiu_Lt60Ef6Cv9YZhCZ32dev5Op71xHo05Qao3jUA0Aw7UrapMrvYki8vduFX6cdEA-ylVQG_IbrGu9IFit_DTLNQigD6VMMLkXrSW-c7kloKNst1ScAsKQ8SQaepAOlgde17XBvnEzhlzfifzUcEl191ve1tL4t3o_Jh77QVqWr2-8vkxcnlDRNddFG5n0R_GlSVfBuM2rkA5Q6yaztVsRzNqv-H2wJvGXU_eqoZl2I2g11cwmxJIAYwShWj-iag0pemACPidAKUm6VPvf4Twfu0oyuocJ2MLmfqh-h4TZFCrWXWNsw8k3R2-28G3islsKoQXmneXW8ctuNbp-kPJaTK1f4q3v91JhRUyS6xs9nEVFR4L-dL3L5gGccJDCcSqzTUy9CMk3HoKbVu02hdORXzD9HTuvmMxqZKDtKZiPUUPtwuI-IChm74gKnqL53Pd_Ny0lIKv89RSBcJT1mgPkcvLJthXUaMBJ7siyW_I6HHBxqlIQWGp8Gvpqudltu88iZFaA4XtpdeEYqpFvLemNujKKHkh7guZWkrnBhxY-TtwEojJy0OihVapJm-8696u1mamsA2CpBzFmEygW-MkVcse5KpRJnn3_RoLeZNyvULMG4ZXj2qoapGs535CxjCFuSrNVSV05fobSC6q2pc_QrXPQIhsZr4mJW1U5ls_Z8D96MbeGmqS9PB75RRaq1Zoce2u9BM65xG0_c3OzK5M9EmxoKQAFjpveVfFe3fTtHvq26-bPZQKQFKXKh3r5Qx0RQy53y7zNFH_drbKAeaf0rv3e6vJodK_mmSpVR9Cy3sm5Noifk4eYACYVp7lp5J3ZknvrL-i8fGopYqaKT8Mp3YXP1UyN6vAX8R-wCwvrbV3k5K1JwTu15yLxIdYqUUcT7UxhOrMuiFhw-nPnJxUkvkvHFuCWUaveIRu9l4WfvrU53MBEHLOYqjQH2CQxi7ee7UBtk67sErb9inIg19hWVemZKuWoCMC1dijv-4Av-O5O69hIFl-2z0-l3Hh126NMj5PRqim8p0d57VMD7MGA_kuqae-gEU3si1sQXKpbPZkuuzXAx96FQOuSyIKjnZoWFtv3wjEM3OV9Mz_nNqXmQ70GjTK77JZqiNSm606g0HPaQNIeF1ei1Umvc7Kp_FB_IRw1hSwNBwC9ed-uNRH83Bv3VVdagf1ACBqVM1tenph7bkKXWaRgEgUWJttPzA4kUCP6YvXJbDLl8pfW3RZRT6vqYg8kJXfltwHuT-ZqOPq2h07MRigqo-YkAptEG4J1G9piYQT_fQeOUCiraNVt7bwELTBRxRmp9hpLXc2lX7owaKeoacrr0jrW0jkiy-011YBgCUmo0YRmSG_U8hw4-DsWGJv2sz2OcZlvaZ37gts6pUCk-TkMNstg31Fw5fLWYckoga2UsQYRKd6JDI4zmQ2xPQ5IiPpDLwUqkJkgeufeYrHRPtatS7U_-GZiod4shaByMCcT9Q8UAiTEYPW_cNJCMPNbddluXe1uQnpU--oWSLsJTcJNRqV-Pz2dOhQdKQh4fG7DwNB9C9C7vVNvNXs4sWM_Usv3j9mjNxC4tlkaIhZQ0VLN4iDUFBqRauLGUhaPGoF_Ix48IWZwvYTWA2RK91Qf7Yl-IoToLzWcB88qmh8Y7Px-qiKEuXMmWaFaK43YgvvRMeeUlDplnk5ZQKVaKm_uoBJVSgs2CPRRHGoP5CSmOOH2NPW08oTAiZXB6MyaIPMDjEa3o7gFAzcaYfsrFeAKMiH70JP-ck8-J5VZ-ByV22lRzz-2tvh5SyWRH7R3s2nzZEThmNYMq2BMSWlDs0xx5XR9LZ8-fNhD4iXtSwLdTNXy485FmJYlonKqgwka3WalxoQmg9sRFwHWSFPMKM7FKhrau0d1M59vKQAPg8ScuRplNfi94Yx-TVXQ0tKUVtj8IyK0jJwQfvFU_-Ove-ht45tdIRrDavZa8_fSUki_JojDlubCcuFfZtxt1HDbo7Dc8I2p5631TOtNixeqPC0k7RgKKl6xukZG4YbrxE27SZdx5uy2JrcKTn5ljd_UtxOnhIE12uk-wIWtGNWT_qJz_wokUfFRlLFeFCiFtQf6wi85sbFy5hQyNsRaK5uXkxr8fXTLINJggUnaYhQmSGV1mZ9FaYa-4xaJeq5beJkg4d4Hs7LEH7qF5iOzhUoDgwWCEB8lEiKveT9iVQFonUXnZdaXE_v9jm_ZrlWrook2X2nRCvLYQmPP7E79JA21mtAP6MqX_M3H5jTehz-an548szsspbGdLS9PH84I6bdMfPtVMCjwYXVUUZWCuIDoIdh9UO7NsRe_-38ewfH2Heu-Lzvn-TE0zVfyXxleyb9zqC_qb_8LUgAbCttBAAA',
            'style'     => 'H4sIAAAAAAAAAyXMSwqAIBAA0KsEbbPPVqWl95h0BEFmYpyFEd29IHjr55teFXeYo-DnjlxZ7BhCcJlJ7cE1Ddt69gGkQJ0aUDMNpWSn2NUkjCyghckSE7rHL__4AokHiRZZAAAA',
            'vanity'    => 'H4sIAAAAAAAAA22S207DMAyGX8XKbtk6Bkxa1laIaQghwSYOF1ymTdYGQhw5ZmVvT1rGDaDEkh3F3__nkLOqnIHaOBeDqq1vCjEVfRmU1seyQtKG-izywZlCVKp-awg_vJajxWKx7KzmVs7OpuFzKcqcKYWGvXK28YVgDD-NR6iE0_AJsxQXKc5T17fEmGzTsozorB62jFarVU9M3jwcGTv0LCt0Gno9UGSVO4nKx3E0ZHfLGh2SHM3n82VSlr2ngNGyRS_JOMV2bxLzMs96aplnrP_YhWPuzI4F_DJ_llSnaZx_n1ZBS2ZXiJY5yCzrum4SCF9NzS16cwjIE6QmE1A7FWMhajJpivJufXe1foDNNWwfNrfr1RPcbO7XL7DdPOWZKvOK_mV_-GT7fVLju_gFfEzrcKNobyIbgi0hJxPp2HBvuEN665HJ2t5qo6E6wPOAGsSGS8j6h8uGH1F-AfgedhsZAgAA',
        ]);
        $factory->setCallbackData([
            'emailCallback' => function($email, $style = null){
                $value = $email;
                $display = 'style="display:' . ['none',' none'][random_int(0,1)] . '"';
                $style = $style ?? random_int(0,5);
                $props[] = "href=\"mailto:$email\"";
        
                $wrap = function($value, $style) use($display){
                    switch($style){
                        case 2: return "<!-- $value -->";
                        case 4: return "<span $display>$value</span>";
                        case 5:
                            $id = 'trovou9u7';
                            return "<div id=\"$id\">$value</div>\n<script>document.getElementById('$id').innerHTML = '';</script>";
                        default: return $value;
                    }
                };
        
                switch($style){
                    case 0: $value = ''; break;
                    case 3: $value = $wrap($email, 2); break;
                    case 1: $props[] = $display; break;
                }
        
                $props = implode(' ', $props);
                $link = "<a $props>$value</a>";
        
                return $wrap($link, $style);
            }
        ]);

        $transcriber = new DataTranscriber($templateData, $factory);

        $template = new ArrayTemplate([
            'doctype',
            'injDocType',
            'head1',
            'injHead1HTMLMsg',
            'robots',
            'injRobotHTMLMsg',
            'nocollect',
            'injNoCollectHTMLMsg',
            'head2',
            'injHead2HTMLMsg',
            'top',
            'injTopHTMLMsg',
            'actMsg',
            'errMsg',
            'customMsg',
            'legal',
            'injLegalHTMLMsg',
            'altLegalMsg',
            'emailCallback',
            'injEmailHTMLMsg',
            'style',
            'injStyleHTMLMsg',
            'vanity',
            'injVanityHTMLMsg',
            'altVanityMsg',
            'bottom',
            'injBottomHTMLMsg',
        ]);

        $hp = new Script($client, $transcriber, $template, $templateData, $factory);
        $hp->handle($host, $port, $script);
    }

    public function handle($host, $port, $script)
    {
        $data = [
            'tag1' => '83cbd646d08d5eee242750cb66f10556',
            'tag2' => '140f6b7ee9ef34b70cde36b40c60960c',
            'tag3' => '3649d4e9bcfd3422fb4f9d22ae0a2a91',
            'tag4' => md5_file(__FILE__),
            'version' => "php-".phpversion(),
            'ip'      => $_SERVER['REMOTE_ADDR'],
            'svrn'    => $_SERVER['SERVER_NAME'],
            'svp'     => $_SERVER['SERVER_PORT'],
            'sn'      => $_SERVER['SCRIPT_NAME']     ?? '',
            'svip'    => $_SERVER['SERVER_ADDR']     ?? '',
            'rquri'   => $_SERVER['REQUEST_URI']     ?? '',
            'phpself' => $_SERVER['PHP_SELF']        ?? '',
            'ref'     => $_SERVER['HTTP_REFERER']    ?? '',
            'uagnt'   => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ];

        $headers = [
            "User-Agent: PHPot {$data['tag2']}",
            "Content-Type: application/x-www-form-urlencoded",
            "Cache-Control: no-store, no-cache",
            "Accept: */*",
            "Pragma: no-cache",
        ];

        $subResponse = $this->client->request("POST", "http://$host:$port/$script", $headers, $data);
        $data = $this->transcriber->transcribe($subResponse->getLines());
        $response = new TextResponse($this->template->render($data));

        $this->serve($response);
    }

    public function serve(Response $response)
    {
        header("Cache-Control: no-store, no-cache");
        header("Pragma: no-cache");

        print $response->getBody();
    }
}

Script::run(__REQUEST_HOST, __REQUEST_PORT, __REQUEST_SCRIPT, __DIR__ . '/phpot_settings.php');

