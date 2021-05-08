<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer;

/**
 * @internal
 */
interface ToolInfoInterface
{
    /**
     * @return mixed[]
     */
    public function getComposerInstallationDetails();
    /**
     * @return string
     */
    public function getComposerVersion();
    /**
     * @return string
     */
    public function getVersion();
    /**
     * @return bool
     */
    public function isInstalledAsPhar();
    /**
     * @return bool
     */
    public function isInstalledByComposer();
    /**
     * @param string $version
     * @return string
     */
    public function getPharDownloadUri($version);
}
