<?php

namespace App\Service;

use App\Entity\ContactRequest;
use App\Entity\ImportCsvFile;
use App\Errors\ValidationError;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactRequestFacade
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param ValidatorInterface $validator
     * @param ObjectManager $em
     */
    public function __construct(ValidatorInterface $validator, ObjectManager $em)
    {
        $this->validator = $validator;
        $this->em = $em;
    }

    /**
     * @param string $rawData
     * @return ContactRequest
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws ValidationError
     */
    public function handleJsonRequest(string $rawData): ContactRequest
    {
        $contactRequest = $this->createContactRequestFromJson($rawData);
        $this->validateContactRequest($contactRequest);
        $this->persistContactRequest($contactRequest);
        $this->em->flush();

        return $contactRequest;
    }

    /**
     * @param string $filePath
     * @param \Closure $progressUpdateCallback
     * @throws ValidationError
     */
    public function handleCsvImport(string $filePath, \Closure $progressUpdateCallback)
    {
        $this->validateCsvFile($filePath);
        $this->processCsv($filePath, $progressUpdateCallback);
    }

    /**
     * @param string $filePath
     * @throws ValidationError
     */
    private function validateCsvFile(string $filePath)
    {
        $file = new ImportCsvFile();
        $file->setFile($filePath);

        $errors = $this->validator->validate($file);
        $flatErrors = [];
        if (count($errors) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $flatErrors[] = $error->getMessage();
            }
            throw new ValidationError(implode(', ', $flatErrors));
        }
    }

    /**
     * @param string $filePath
     * @param \Closure $progressUpdateCallback
     * @throws ValidationError
     */
    private function processCsv(string $filePath, \Closure $progressUpdateCallback)
    {
        if (($handle = fopen($filePath, "r")) === false) {
            throw new \RuntimeException('Cannot read file ' . $filePath);
        }

        $totalBytesCount = filesize($filePath);

        while (($row = fgetcsv($handle, 0, ",")) !== false) {
            if (count($row) < 2) {
                continue;
            }

            $contactRequest = new ContactRequest();
            $contactRequest->setEmail($row[0]);
            $contactRequest->setMessage($row[1]);

            $readBytesCount = strlen(implode("", $row)) + 1;
            $progressUpdateCallback($totalBytesCount, $readBytesCount);

            $this->validateContactRequest($contactRequest);
            $this->persistContactRequest($contactRequest);
        }

        fclose($handle);
        $this->em->flush();
    }

    /**
     * @param string $rawJson
     * @return ContactRequest
     * @throws ValidationError
     */
    private function createContactRequestFromJson(string $rawJson): ContactRequest
    {
        $encoders = [new JsonEncoder(), new CsvEncoder()];
        $normalizers = [new GetSetMethodNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        try {
            /** @var ContactRequest $contactRequest */
            $contactRequest = $serializer->deserialize($rawJson, ContactRequest::class, 'json');
        } catch (\Exception $e) {
            throw new ValidationError($e->getMessage());
        }

        return $contactRequest;
    }

    /**
     * @param ContactRequest $contactRequest
     */
    private function persistContactRequest(ContactRequest $contactRequest)
    {
        $contactRequest->setCreatedAt(new \DateTime());

        $this->em->persist($contactRequest);
    }

    /**
     * @param ContactRequest $contactRequest
     * @throws ValidationError
     */
    private function validateContactRequest(ContactRequest $contactRequest)
    {
        $errors = $this->validator->validate($contactRequest);
        $flatErrors = [];
        if (count($errors) > 0) {
            /** @var ConstraintViolation $error */
            foreach ($errors as $error) {
                $flatErrors[$error->getPropertyPath()] = $error->getMessage();
            }
            throw new ValidationError(json_encode($flatErrors));
        }
    }
}