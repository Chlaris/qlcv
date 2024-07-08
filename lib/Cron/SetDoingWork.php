<?php
namespace OCA\QLCV\Cron;

use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\ILogger;
use OCA\QLCV\Service\WorkService;
use DateTime;

class SetDoingWork extends TimedJob
{
    private $workService;
    private $logger; // Định nghĩa thuộc tính logger

    public function __construct(
        ITimeFactory $time,
        WorkService $workService,
        ILogger $logger // Thêm ILogger vào constructor
    ) {
        parent::__construct($time);
        $this->workService = $workService;
        $this->logger = $logger; // Khởi tạo logger
        $this->setInterval(60 * 10); // 10 minutes
    }

    protected function run($arguments)
    {
        $today = new DateTime();
        $today->setTime(0, 0);
        $todayTimestamp = $today->getTimestamp();
        // Log thông tin bắt đầu
        $this->logger->debug("Cron job 'SetDoingWork' started.", ['app' => 'QLCV']);
        $this->logger->debug("Timestamp: {$todayTimestamp}", ['app' => 'QLCV']);

        try {
            $works = $this->workService->setDoingWork();
            // Có thể log thêm thông tin thành công nếu cần thiết
        } catch (\Exception $e) {
            // Log lỗi khi bắt được ngoại lệ
            $this->logger->error("Error in cron: " . $e->getMessage(), ['app' => 'QLCV']);
            // Cân nhắc việc throw ngoại lệ nếu bạn muốn thông báo lỗi này ra ngoài chron job
        }
    }
}