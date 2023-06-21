<div class="trace trace-as-html" id="trace-box-<?php 
namespace ECSPrefix202306;

echo $index;
?>">
    <div class="trace-details">
        <div class="trace-head">
            <div class="sf-toggle" data-toggle-selector="#trace-html-<?php 
echo $index;
?>" data-toggle-initial="<?php 
echo $expand ? 'display' : '';
?>">
                <span class="icon icon-close"><?php 
echo $this->include('assets/images/icon-minus-square-o.svg');
?></span>
                <span class="icon icon-open"><?php 
echo $this->include('assets/images/icon-plus-square-o.svg');
?></span>
                <?php 
$separator = \strrpos($exception['class'], '\\');
$separator = \false === $separator ? 0 : $separator + 1;
$namespace = \substr($exception['class'], 0, $separator);
$class = \substr($exception['class'], $separator);
?>
                <?php 
if ('' === $class) {
    ?>
                    <br>
                <?php 
} else {
    ?>
                    <h3 class="trace-class">
                        <?php 
    if ('' !== $namespace) {
        ?>
                            <span class="trace-namespace"><?php 
        echo $namespace;
        ?></span>
                        <?php 
    }
    ?>
                        <?php 
    echo $class;
    ?>
                    </h3>
                <?php 
}
?>
                <?php 
if ($exception['message'] && $index > 1) {
    ?>
                    <p class="break-long-words trace-message"><?php 
    echo $this->escape($exception['message']);
    ?></p>
                <?php 
}
?>
            </div>
            <?php 
if (\count($exception['data'] ?? [])) {
    ?>
                <details class="exception-properties-wrapper">
                    <summary>Show exception properties</summary>
                    <div class="exception-properties">
                        <?php 
    echo $this->dumpValue($exception['data']);
    ?>
                    </div>
                </details>
            <?php 
}
?>
        </div>

        <div id="trace-html-<?php 
echo $index;
?>" class="sf-toggle-content">
        <?php 
$isFirstUserCode = \true;
foreach ($exception['trace'] as $i => $trace) {
    $isVendorTrace = $trace['file'] && (\strpos($trace['file'], '/vendor/') !== \false || \strpos($trace['file'], '/var/cache/') !== \false);
    $displayCodeSnippet = $isFirstUserCode && !$isVendorTrace;
    if ($displayCodeSnippet) {
        $isFirstUserCode = \false;
    }
    ?>
            <div class="trace-line <?php 
    echo $isVendorTrace ? 'trace-from-vendor' : '';
    ?>">
                <?php 
    echo $this->include('views/trace.html.php', ['prefix' => $index, 'i' => $i, 'trace' => $trace, 'style' => $isVendorTrace ? 'compact' : ($displayCodeSnippet ? 'expanded' : '')]);
    ?>
            </div>
            <?php 
}
?>
        </div>
    </div>
</div>
<?php 
