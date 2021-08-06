#!/usr/bin/env php
<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Bridge\Twig\Command\LintCommand;
use Symfony\Component\Console\Application;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$environment = new Environment(new FilesystemLoader());

(new Application('twig/lint'))
    ->add(new LintCommand($environment))
    ->getApplication()
    ->setDefaultCommand('lint:twig', true)
    ->run();
