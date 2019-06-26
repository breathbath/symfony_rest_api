# Library description
[![Travis Status](https://travis-ci.com/breathbath/symfony_rest_api.svg?branch=master)](https://travis-ci.com/breathbath/symfony_rest_api)

Current repository is created for demonstrating how to build RESTFull API with Symfony 4.2 framework. It contains minimal setup and an example for CRUD operations for an imaginary Customer entity.

## Commands list

### Init the application

Please make sure you have docker and docker-compose installed on your machine before running it.

    ./start.sh init

### Run tests
    
    ./start.sh tests #run all tests
    ./start.sh tests -f #run functional tests   
    ./start.sh tests -u #run unit tests   

## Api endpoints

### To create contact form request:

**Method**: POST

**URL**: `http://localhost:81/v1/contact-requests`

**BODY**:

       {
       	"email": "no@mail.me",
       	"message": "My message"
       }

### To list contact form request:

**Method**: GET

**URL**: `http://localhost:81/v1/customers`

### To read docs:

**Method**: GET

**URL**: `http://localhost:81/v1/doc`

## Commands

### To list all commands

    bash start.sh cmd

### To import csv with the list of contact requests

    bash start.sh cmd app:import:contact-requests importExample.csv