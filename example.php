<?php
/**
 * Project:     phpCutyCapt
 * File:        example.php
 *
 * This class is free software for generating website screenshots
 * using CutyCapt(http://cutycapt.sourceforge.net) via X virtual
 * framebuffer (xvfb) on linux systems
 *
 * @link http://moneyseeker.ru/
 * @copyright 2013 Stanislav Fedotov
 * @author Stanislav Fedotov <me at moneyseeker dot ru>
 * @version 1.0
 */

require_once 'phpCutyCapt.php';

$capture = new \phpCutyCapt\phpCutyCapt("http://moneyseeker.ru", "moneyseeker.png", 1280, true);
if($capture->screenshot())
    echo "Screenshot taken";