phpCutyCapt
===========

PHP wrapper for getting website screenshots via CutyCapt http://cutycapt.sourceforge.net

Warning
-------

CutyCapt binary must be in the same directory

Installing CutyCapt
-------

For instructions on how to install the CutyCapt binary, use documentation on http://cutycapt.sourceforge.net

Suggestion
-------

There are some sites, containing script know as "infinite scroll". While taking screenshots from them with option to enable javascript in CutyCapt, it gets to "infinite" screenshotting :(
I'm not good at LibQtWebkit, so the only thing I got to is to change in CutyCapt.cpp the line:

mPage->setViewportSize( mainFrame->contentsSize() );

to:

mPage->setViewportSize(QSize(1280, 1024));

I know it's Indian code, but... This is what we have.
I'll appreciate your suggestions on how to solve this problem
Good luck.
