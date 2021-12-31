<?php

/**
 * coralic-kirby-plainkit
 *
 * This plugin is required for the dev environment to work.
 * Don't remove it!
 */

function loadCSS($file)
{
  return getEnv('ENV') === "DEVELOPMENT" ? css('dist/dev/css/' . $file) : css('dist/prod/css/' . $file);
}


function loadJS($file)
{
  return getEnv('ENV') === "DEVELOPMENT" ? js('dist/dev/js/' . $file) : js('dist/prod/js/' . $file);
}
