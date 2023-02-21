<?php

namespace Predisx\Profiles;

class ServerVersion12 extends ServerProfile {
    public function getVersion() { return '1.2'; }
    public function getSupportedCommands() {
        return array(
            /* ---------------- Redis 1.2 ---------------- */

            /* commands operating on the key space */
            'exists'                    => '\Predisx\Commands\KeyExists',
            'del'                       => '\Predisx\Commands\KeyDelete',
            'type'                      => '\Predisx\Commands\KeyType',
            'keys'                      => '\Predisx\Commands\KeyKeysV12x',
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
        );
    }
}
