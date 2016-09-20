#!/usr/bin/env php
<?php
/**
 * The script can merge zh-cn language files of chanzhiEPS to one file.  
 */
if(empty($argv[1])) die("Must give a chanzhieps root path.\n");

$chanzhiRoot = $argv[1];                        /* Root of chanzhiEPS. */
$mergedPath  = empty($argv[2]) ? '' : $argv[2]; /* The merged file will be write in this directory. */
$moduleRoot  = $chanzhiRoot . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . 'module' . DIRECTORY_SEPARATOR;
$handle = opendir($moduleRoot);  
if($handle)  
{  
    $langFiles = array();
    while(($module = readdir($handle)) !== false)  
    {  
        $filePath = $moduleRoot . $module . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'zh-cn.php';
        if(!file_exists($filePath)) continue;

        /* Remove the comments and start tag of php. */
        $langFiles[$module] = preg_replace("/\/\*.*?\*\/\n/s", '', ltrim(file_get_contents($filePath), "<?php"));
    }  
    closedir($handle);

    $mergedFile = 'zh-cn.php';
    if($mergedPath)
    {
        if(!file_exists($mergedPath)) @mkdir($mergedPath, 0777, true);
        $mergedFile = $mergedPath . DIRECTORY_SEPARATOR . $mergedFile;
    }
    file_put_contents($mergedFile, "<?php\n");
    $fh = @fopen($mergedFile, 'a');
    ksort($langFiles);

    if(isset($langFiles['common']))
    {
        fwrite($fh, "/* common */");
        fwrite($fh, $langFiles['common']);
        unset($langFiles['common']);
    }

    foreach($langFiles as $module => $content)
    {
        fwrite($fh, "/* " . $module . " */");
        fwrite($fh, $content);
    }
    fclose($fh);
}

echo "merge lang files down!\n";
