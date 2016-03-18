<?php

namespace Nz\CronBundle\Crontab;

/**
 * Description of Manager
 *
 * @author nz
 */
class Manager
{

    public function add_cronjob($command)
    {
        $output = false;
        if (is_string($command) && !empty($command) && $this->cronjob_exists($command) === FALSE) {

            $output = shell_exec('(crontab -l; echo "' . $command . '") | crontab -');
        }

        return $output;
    }

    public function remove_cronjob($command)
    {
        exec('crontab -l', $crontab);

        $commands = [];
        if (isset($crontab) && is_array($crontab)) {
            foreach ($crontab as $cmd) {
                if ($cmd == $command || empty($cmd)) {
                    continue;
                }
                $commands[] = $cmd;
            }

            $this->set_cronjobs($commands);
        }
    }

    protected function set_cronjobs($commands = array())
    {
        $output = shell_exec('echo "' . implode("\r\n", $commands) . '" | crontab -');
        return $output;
    }

    protected function cronjob_exists($command)
    {
        $cronjob_exists = false;
        exec('crontab -l', $crontab);
        if (isset($crontab) && is_array($crontab)) {
            $crontab = array_flip($crontab);

            if (isset($crontab[$command])) {
                $cronjob_exists = true;
            }
        }
        return $cronjob_exists;
    }
}
