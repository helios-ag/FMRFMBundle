<?php

namespace FM\RFMBundle\Composer;

use Composer\Script\CommandEvent;
use Composer\Script\Event;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;
use Sensio\Bundle\DistributionBundle\Composer\ScriptHandler;

class RFMScriptHandler extends ScriptHandler
{
    /**
     * @param CommandEvent|Event $event
     *
     * @return string
     */
    public static function copy(Event $event)
    {
      $extras = $event->getComposer()->getPackage()->getExtra();
      $version = \Symfony\Component\HttpKernel\Kernel::MAJOR_VERSION;

      $baseDir = 'vendor/servocoder/richfilemanager/';

      $dirsToCopy = [
        'config',
        'images',
        'languages',
        'libs',
        'src',
        'themes'
      ];

      $destinationDir = $version < 4 ? 'web/assets/' : 'public/assets/';

      if (isset($extras['rfm-dir'])) {
        $directory = $extras['rfm-dir'];
      }

      $fs = new Filesystem;
      $io = $event->getIO();
      foreach ($dirsToCopy as $dir) {
          $from = $baseDir . $dir;
          $to = $destinationDir . $dir;
          $isRenameFile = substr($to, -1) != '/' && !is_dir($from);
          if (file_exists($to) && !is_dir($to) && !$isRenameFile) {
              throw new \InvalidArgumentException('Destination directory is not a directory.');
          }
          try {
              if ($isRenameFile) {
                  $fs->mkdir(dirname($to));
              } else {
                  $fs->mkdir($to);
              }
          } catch (IOException $e) {
              throw new \InvalidArgumentException(sprintf('<error>Could not create directory %s.</error>', $to));
          }

          if (false === file_exists($from)) {
              throw new \InvalidArgumentException(sprintf('<error>Source directory or file "%s" does not exist.</error>', $from));
          }

          if (is_dir($from)) {
              $finder = new Finder;
              $finder->files()->ignoreDotFiles(false)->in($from);
              foreach ($finder as $file) {
                  $dest = sprintf('%s/%s', $to, $file->getRelativePathname());
                  try {
                      $fs->copy($file, $dest);
                  } catch (IOException $e) {
                      throw new \InvalidArgumentException(sprintf('<error>Could not copy %s</error>', $file->getBaseName()));
                  }
              }
          } else {
              try {
                  if ($isRenameFile) {
                      $fs->copy($from, $to);
                  } else {
                      $fs->copy($from, $to.'/'.basename($from));
                  }
              } catch (IOException $e) {
                  throw new \InvalidArgumentException(sprintf('<error>Could not copy %s</error>', $from));
              }
          }
          $io->write(sprintf('Copied asset file(s) from <comment>%s</comment> to <comment>%s</comment>.', $from, $to));
      }
    }
}
