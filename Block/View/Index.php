<?php

namespace PhilTurner\LogViewer\Block\View;

use PhilTurner\LogViewer\Helper\ReadLogFileTrait;

class Index extends \Magento\Framework\View\Element\Template
{
    use ReadLogFileTrait {
        fetch as fetchLogFileBlocks;
    }

    /**
     * @var \PhilTurner\LogViewer\Helper\Data
     */
    protected $logDataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \PhilTurner\LogViewer\Helper\Data                $logDataHelper
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \PhilTurner\LogViewer\Helper\Data $logDataHelper,
        array $data = []
    )
    {
        $this->logDataHelper = $logDataHelper;
        parent::__construct($context, $data);
    }

    public function getLogFile()
    {
        return $this->logDataHelper->getLastLinesOfFile($this->getFileName(), 10);
    }

    /**
     * Get logs
     *
     * @return array
     */
    public function getLogFileBlocks(): array
    {
        return $this->fetchLogFileBlocks($this->logFile(), $this->getLimit(), $this->getStart());
    }

    public function getLimit(): int
    {
        return (int) $this->getRequest()->getParam('limit', 10) ?: 10;
    }

    public function getStart(): int
    {
        return (int) $this->getRequest()->getParam('start', 0);
    }

    public function getFileName()
    {
        return $this->getRequest()->getParam('file');
    }

    /**
     * Get limit URL
     *
     * @param int $limit
     * @return string
     */
    public function getLimitUrl(int $limit): string
    {
        return $this->getUrl('*/*/*', [
            '_current' => true,
            'limit'    => $limit,
            'file'     => $this->getFileName(),
        ]);
    }

    /**
     * Get start URL
     *
     * @param int $start
     * @return string
     */
    public function getStartUrl(int $start): string
    {
        return $this->getUrl('*/*/*', [
            '_current'     => true,
            'start'        => $start,
            'file'         => $this->getFileName(),
        ]);
    }

    /**
     * Get back URL
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('*/grid/', ['_current' => true]);
    }

    /**
     * Get starts list
     *
     * @param int $max
     * @return array
     */
    public function getStarts($max = 10)
    {
        $start = $this->getStart() - $this->getLimit() * 2;
        $start = $start > 0 ? $start : 0;
        if ($start > $this->getLimit() * 3) {
            $step = ceil($start / 4);
            $step -= $step % $this->getLimit();

            return array_merge(
                range(0, $start - $this->getLimit(), $step),
                range($start, $this->getLimit() * ($max - 1) + $start, $this->getLimit())
            );
        }

        return range(0, $this->getLimit() * ($max - 1) + $start, $this->getLimit());
    }

    /**
     * Get starts list
     *
     * @return array
     */
    public function getLimits()
    {
        return [10, 20, 30, 50, 100, 500, 1000];
    }

    /**
     * Get full path to log file
     *
     * @return string
     */
    private function logFile(): string
    {
        return $this->logDataHelper->getPath().DIRECTORY_SEPARATOR.$this->getFileName();
    }

}
