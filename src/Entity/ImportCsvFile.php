<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class ImportCsvFile
{
    /**
     * @Assert\NotBlank()
     * @Assert\File(
     *     mimeTypes = {"text/plain"},
     *     notFoundMessage = "File {{ file }} cannot be found"
     * )
     */
    private $file;

    /**
     * @return string
     */
    public function getFile(): ?string
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile(string $file): void
    {
        $this->file = $file;
    }
}