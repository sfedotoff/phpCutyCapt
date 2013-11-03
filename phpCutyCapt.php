<?php

namespace phpCutyCapt;

/**
 * Project:     phpCutyCapt
 * File:        phpCutyCapt.php
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

class phpCutyCapt {
    /**
     * The url of the website page, we're making the screenshot of
     * This must include protocol (http:// or https://)
     *
     * @var string
     */
    protected $parseURL;

    /**
     * Minimum width of the page to take screenshot
     *
     * @var int
     */
    protected $minWidth;

    /**
     * File path to output the screenshot to
     * CutyCapt has an heuristic format recognizer, so you can
     * use one of the following extensions:
     * svg,ps,pdf,itext,html,rtree,png,jpeg,mng,tiff,gif,bmp,ppm,xbm,xpm
     * I suggest png or jpg
     *
     * @var string
     */
    protected $outFile;

    /**
     * Enable plugins, such as Flash and others
     *
     * @var bool
     */
    protected $pluginsEnable;

    /**
     * Delay. Amount of time in seconds to wait before taking shot
     * after the page is loaded
     *
     * @var int
     */
    protected $delay;

    /**
     * @param $parseUrl
     * @param $outFile
     * @param int $minWidth
     * @param bool $pluginsEnable
     * @param int $delay
     * @throws Exception
     */
    public function __construct($parseUrl, $outFile, $minWidth = 1024, $pluginsEnable = true, $delay = 0)
    {
        $this->parseURL       = escapeshellarg($parseUrl);
        $this->outFile        = $outFile;
        $this->minWidth       = $minWidth;
        $this->minWidth       = $minWidth;
        $this->pluginsEnable  = $pluginsEnable;
        $this->delay          = $delay;

        if(!is_dir(dirname($outFile)) OR !is_writeable(dirname($outFile))) {
            throw new Exception('Directory for screenshotfile does not exist');
        }
    }

    /**
     * Create screenshot and tell if it was successfully generated
     * @return bool
     */
    public function screenshot()
    {
        $cmd  = 'xvfb-run --server-args="-screen 0, 1024x768x24" ./CutyCapt';
        $cmd .= '--min-width='.$this->minWidth;
        $cmd .= '--url='.$this->parseURL;
        $cmd .= ' --out='.$this->outFile;
        $cmd .= ' --delay='.$this->delay;
        $cmd .= ($this->pluginsEnable) ? ' --plugins=on' : ' --plugins=off';

        exec($cmd);

        if(file_exists($this->outFile)) {
            if(filesize($this->outFile)>1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Delete file after managing actions for it
     */
    public function clean()
    {
        unlink($this->outFile);
    }
} 