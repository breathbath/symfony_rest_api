<?php

namespace App\Model\Identity;

class UidGenerator
{
    /**
     * @param string $email
     * @param \DateTime $time
     * @throws \InvalidArgumentException
     * @return string
     */
    public function generateUid(string $email, \DateTime $time): string
    {
        if ($email === '') {
            throw new \InvalidArgumentException('Empty email value');
        }

        return sprintf('%d:%d', crc32($email), crc32($time->format('YmdHis')));
    }
}