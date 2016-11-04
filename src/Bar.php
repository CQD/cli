<?php

namespace Q\Cli;

/**
 * text message 33/123 50% [====>       ]
 *
 * Usage:
 *
 *  $total = count($old_rows);
 *  $bar = new Bar($total, "Removing old data");
 *  foreach ($old_rows as $row) {
 *      $row->deleteMe();
 *      $bar->tick();
 *  }
 *  $bar->finish();
 *
 */
class Bar
{
    private $total = 0;
    private $msg = '';
    private $width = 0;
    private $bar_width = 0;

    private $progress = 0;
    private $per = 0;

    private $start_time = 0;
    private $last_time = 0;

    public function __construct($total, $msg = '')
    {
        $this->total = $total;
        $this->msg = $msg;
        $this->last_time = 0;
        $this->start_time = time();
        $this->render();
    }

    public function tick($count = 1, $msg = null)
    {
        $this->progress += $count;
        $per = (int) floor(($this->progress / $this->total) * 100 );
        $per = $per > 100 ? 100 : $per;

        $do_render = $per !== $this->per || microtime(true) - $this->last_time >= 1;

        $this->per = $per;

        if ($do_render) {
            $this->render($msg);
        }
    }

    public function render($msg = null)
    {
        $msg = $msg ? "{$this->msg} - {$msg}" : $this->msg;

        $_c = chr(27);
        $_rb = "{$_c}[41m";      // red background
        $_gb = "{$_c}[42m";      // green background
        $_yb = "{$_c}[43m";      // yellow background
        $_bb = "{$_c}[44m";      // blue background
        $_df = "{$_c}[30m";      // dark foreground
        $_rf = "{$_c}[31m";      // red foreground
        $_gf = "{$_c}[32m";      // green foreground
        $_yf = "{$_c}[33m";      // yellow foreground
        $_bf = "{$_c}[34m";      // blue foreground
        $_mf = "{$_c}[35m";      // magenta foreground
        $_cf = "{$_c}[36m";      // cyan foreground
        $_wf = "{$_c}[37m";      // white foreground
        $_r = "{$_c}[0m";        // color reset

        $this->width = (int) exec('tput cols');
        $this->bar_width = $this->width - strlen($msg) - strlen($this->total) * 2 - 32;

        $t = strlen($this->total);
        $bar_len = (int) floor($this->progress * $this->bar_width / $this->total);
        $space_len = $this->bar_width - $bar_len;

        if ($bar_len < 0) {
            $bar_len = 0;
        }
        if ($space_len < 0) {
            $space_len = 0;
            $bar_len = $this->bar_width; // 避免長度超過...
        }

        $time_used = time() - $this->start_time;

        echo sprintf(
            "\r %s {$_yf}%{$t}d/%d{$_r} (%3d%%) {$_cf}%2dd, %02d:%02d:%02d{$_r} [ {$_gf}%s{$_yf}🍺{$_r}%s ]",
            $msg,
            $this->progress,
            $this->total,
            $this->per,
            $time_used / 86400,
            $time_used / 3600 % 24,
            $time_used / 60 % 60,
            $time_used % 60,
            str_repeat('▒', $bar_len),
            str_repeat(' ', $space_len)
        );

        $this->last_time = microtime(true);
    }

    public function finish()
    {
        $this->render();
        echo "\n";
    }
}
