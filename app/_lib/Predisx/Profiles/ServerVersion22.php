<?php

namespace Predisx\Profiles;

class ServerVersion22 extends ServerProfile {
    public function getVersion() { return '2.2'; }
    public function getSupportedCommands() {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => '\Predisx\Commands\KeyExists',
            'del'                       => '\Predisx\Commands\KeyDelete',
            'type'                      => '\Predisx\Commands\KeyType',
            'keys'                      => '\Predisx\Commands\KeyKeys',
            'randomkey'                 => '\Predisx\Commands\KeyRandom',
            'rename'                    => '\Predisx\Commands\KeyRename',
            'renamenx'                  => '\Predisx\Commands\KeyRenamePreserve',
            'expire'                    => '\Predisx\Commands\KeyExpire',
            'expireat'                  => '\Predisx\Commands\KeyExpireAt',
            'ttl'                       => '\Predisx\Commands\KeyTimeToLive',
            'move'                      => '\Predisx\Commands\KeyMove',
            'sort'                      => '\Predisx\Commands\KeySort',

            /* commands operating on string values */
            'set'                       => '\Predisx\Commands\StringSet',
            'setnx'                     => '\Predisx\Commands\StringSetPreserve',
            'mset'                      => '\Predisx\Commands\StringSetMultiple',
            'msetnx'                    => '\Predisx\Commands\StringSetMultiplePreserve',
            'get'                       => '\Predisx\Commands\StringGet',
            'mget'                      => '\Predisx\Commands\StringGetMultiple',
            'getset'                    => '\Predisx\Commands\StringGetSet',
            'incr'                      => '\Predisx\Commands\StringIncrement',
            'incrby'                    => '\Predisx\Commands\StringIncrementBy',
            'decr'                      => '\Predisx\Commands\StringDecrement',
            'decrby'                    => '\Predisx\Commands\StringDecrementBy',

            /* commands operating on lists */
            'rpush'                     => '\Predisx\Commands\ListPushTail',
            'lpush'                     => '\Predisx\Commands\ListPushHead',
            'llen'                      => '\Predisx\Commands\ListLength',
            'lrange'                    => '\Predisx\Commands\ListRange',
            'ltrim'                     => '\Predisx\Commands\ListTrim',
            'lindex'                    => '\Predisx\Commands\ListIndex',
            'lset'                      => '\Predisx\Commands\ListSet',
            'lrem'                      => '\Predisx\Commands\ListRemove',
            'lpop'                      => '\Predisx\Commands\ListPopFirst',
            'rpop'                      => '\Predisx\Commands\ListPopLast',
            'rpoplpush'                 => '\Predisx\Commands\ListPopLastPushHead',

            /* commands operating on sets */
            'sadd'                      => '\Predisx\Commands\SetAdd',
            'srem'                      => '\Predisx\Commands\SetRemove',
            'spop'                      => '\Predisx\Commands\SetPop',
            'smove'                     => '\Predisx\Commands\SetMove',
            'scard'                     => '\Predisx\Commands\SetCardinality',
            'sismember'                 => '\Predisx\Commands\SetIsMember',
            'sinter'                    => '\Predisx\Commands\SetIntersection',
            'sinterstore'               => '\Predisx\Commands\SetIntersectionStore',
            'sunion'                    => '\Predisx\Commands\SetUnion',
            'sunionstore'               => '\Predisx\Commands\SetUnionStore',
            'sdiff'                     => '\Predisx\Commands\SetDifference',
            'sdiffstore'                => '\Predisx\Commands\SetDifferenceStore',
            'smembers'                  => '\Predisx\Commands\SetMembers',
            'srandmember'               => '\Predisx\Commands\SetRandomMember',

            /* commands operating on sorted sets */
            'zadd'                      => '\Predisx\Commands\ZSetAdd',
            'zincrby'                   => '\Predisx\Commands\ZSetIncrementBy',
            'zrem'                      => '\Predisx\Commands\ZSetRemove',
            'zrange'                    => '\Predisx\Commands\ZSetRange',
            'zrevrange'                 => '\Predisx\Commands\ZSetReverseRange',
            'zrangebyscore'             => '\Predisx\Commands\ZSetRangeByScore',
            'zcard'                     => '\Predisx\Commands\ZSetCardinality',
            'zscore'                    => '\Predisx\Commands\ZSetScore',
            'zremrangebyscore'          => '\Predisx\Commands\ZSetRemoveRangeByScore',

            /* connection related commands */
            'ping'                      => '\Predisx\Commands\ConnectionPing',
            'auth'                      => '\Predisx\Commands\ConnectionAuth',
            'select'                    => '\Predisx\Commands\ConnectionSelect',
            'echo'                      => '\Predisx\Commands\ConnectionEcho',
            'quit'                      => '\Predisx\Commands\ConnectionQuit',

            /* remote server control commands */
            'info'                      => '\Predisx\Commands\ServerInfo',
            'slaveof'                   => '\Predisx\Commands\ServerSlaveOf',
            'monitor'                   => '\Predisx\Commands\ServerMonitor',
            'dbsize'                    => '\Predisx\Commands\ServerDatabaseSize',
            'flushdb'                   => '\Predisx\Commands\ServerFlushDatabase',
            'flushall'                  => '\Predisx\Commands\ServerFlushAll',
            'save'                      => '\Predisx\Commands\ServerSave',
            'bgsave'                    => '\Predisx\Commands\ServerBackgroundSave',
            'lastsave'                  => '\Predisx\Commands\ServerLastSave',
            'shutdown'                  => '\Predisx\Commands\ServerShutdown',
            'bgrewriteaof'              => '\Predisx\Commands\ServerBackgroundRewriteAOF',


            /* ---------------- Redis 2.0 ---------------- */

            /* commands operating on string values */
            'setex'                     => '\Predisx\Commands\StringSetExpire',
            'append'                    => '\Predisx\Commands\StringAppend',
            'substr'                    => '\Predisx\Commands\StringSubstr',

            /* commands operating on lists */
            'blpop'                     => '\Predisx\Commands\ListPopFirstBlocking',
            'brpop'                     => '\Predisx\Commands\ListPopLastBlocking',

            /* commands operating on sorted sets */
            'zunionstore'               => '\Predisx\Commands\ZSetUnionStore',
            'zinterstore'               => '\Predisx\Commands\ZSetIntersectionStore',
            'zcount'                    => '\Predisx\Commands\ZSetCount',
            'zrank'                     => '\Predisx\Commands\ZSetRank',
            'zrevrank'                  => '\Predisx\Commands\ZSetReverseRank',
            'zremrangebyrank'           => '\Predisx\Commands\ZSetRemoveRangeByRank',

            /* commands operating on hashes */
            'hset'                      => '\Predisx\Commands\HashSet',
            'hsetnx'                    => '\Predisx\Commands\HashSetPreserve',
            'hmset'                     => '\Predisx\Commands\HashSetMultiple',
            'hincrby'                   => '\Predisx\Commands\HashIncrementBy',
            'hget'                      => '\Predisx\Commands\HashGet',
            'hmget'                     => '\Predisx\Commands\HashGetMultiple',
            'hdel'                      => '\Predisx\Commands\HashDelete',
            'hexists'                   => '\Predisx\Commands\HashExists',
            'hlen'                      => '\Predisx\Commands\HashLength',
            'hkeys'                     => '\Predisx\Commands\HashKeys',
            'hvals'                     => '\Predisx\Commands\HashValues',
            'hgetall'                   => '\Predisx\Commands\HashGetAll',

            /* transactions */
            'multi'                     => '\Predisx\Commands\TransactionMulti',
            'exec'                      => '\Predisx\Commands\TransactionExec',
            'discard'                   => '\Predisx\Commands\TransactionDiscard',

            /* publish - subscribe */
            'subscribe'                 => '\Predisx\Commands\PubSubSubscribe',
            'unsubscribe'               => '\Predisx\Commands\PubSubUnsubscribe',
            'psubscribe'                => '\Predisx\Commands\PubSubSubscribeByPattern',
            'punsubscribe'              => '\Predisx\Commands\PubSubUnsubscribeByPattern',
            'publish'                   => '\Predisx\Commands\PubSubPublish',

            /* remote server control commands */
            'config'                    => '\Predisx\Commands\ServerConfig',


            /* ---------------- Redis 2.2 ---------------- */

            /* commands operating on the key space */
            'persist'                   => '\Predisx\Commands\KeyPersist',

            /* commands operating on string values */
            'strlen'                    => '\Predisx\Commands\StringStrlen',
            'setrange'                  => '\Predisx\Commands\StringSetRange',
            'getrange'                  => '\Predisx\Commands\StringGetRange',
            'setbit'                    => '\Predisx\Commands\StringSetBit',
            'getbit'                    => '\Predisx\Commands\StringGetBit',

            /* commands operating on lists */
            'rpushx'                    => '\Predisx\Commands\ListPushTailX',
            'lpushx'                    => '\Predisx\Commands\ListPushHeadX',
            'linsert'                   => '\Predisx\Commands\ListInsert',
            'brpoplpush'                => '\Predisx\Commands\ListPopLastPushHeadBlocking',

            /* commands operating on sorted sets */
            'zrevrangebyscore'          => '\Predisx\Commands\ZSetReverseRangeByScore',

            /* transactions */
            'watch'                     => '\Predisx\Commands\TransactionWatch',
            'unwatch'                   => '\Predisx\Commands\TransactionUnwatch',

            /* remote server control commands */
            'object'                    => '\Predisx\Commands\ServerObject',
        );
    }
}
