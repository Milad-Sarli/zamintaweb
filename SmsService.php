<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private ?string $apiKey;

    private string $fromNumber;

    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.ippanel.api_key'); // Optional for pattern-based SMS
        $this->fromNumber = config('services.ippanel.from_number', '+98BANK');
        $this->baseUrl = 'https://api2.ippanel.com/api/v1';
    }

    /**
     * ارسال پیامک ساده
     */
    public function sendSms(string $to, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/sms/send/webservice/single', [
                'recipient' => $to,
                'sender' => $this->fromNumber,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Log::info('SMS sent successfully', [
                    'to' => $to,
                    'response' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => 'پیامک با موفقیت ارسال شد',
                    'data' => $data,
                ];
            }

            Log::error('SMS sending failed', [
                'to' => $to,
                'status' => $response->status(),
                'response' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک',
                'error' => $response->body(),
            ];

        } catch (Exception $e) {
            Log::error('SMS service exception', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در اتصال به سرویس پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * ارسال کد تایید
     */
    public function sendVerificationCode(string $phoneNumber, string $code): array
    {
        $message = "کد تایید شما: {$code}\nاین کد تا 5 دقیقه معتبر است.";

        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * ارسال پیامک بازیابی رمز عبور با استفاده از پترن
     */
    public function sendPasswordResetCode(string $phoneNumber, string $code): array
    {
        try {
            // اطلاعات احراز هویت - باید در .env تنظیم شوند
            $username = config('services.ippanel.username');
            $password = config('services.ippanel.password');
            $from = config('services.ippanel.from_number', '+98BANK');
            $pattern_code = config('services.ippanel.reset_pattern_code');

            // آماده‌سازی داده‌ها
            $to = [$phoneNumber];
            $input_data = ['code' => $code];

            // ساخت URL
            $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

            // ارسال درخواست با cURL
            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('cURL error in password reset SMS', [
                    'error' => $error,
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرویس پیامک',
                    'error' => $error,
                ];
            }

            // بررسی پاسخ
            $responseData = json_decode($response, true);

            if ($httpCode === 200) {
                Log::info('Password reset SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'کد بازیابی رمز عبور با موفقیت ارسال شد',
                    'data' => $responseData,
                ];
            }

            Log::error('Password reset SMS sending failed', [
                'phone' => $phoneNumber,
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال کد بازیابی رمز عبور',
                'error' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Password reset SMS service exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در سرویس ارسال پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * ارسال هشدار عدم ثبت حضور و غیاب با استفاده از پترن
     */
    public function sendAttendanceReminder(string $phoneNumber, string $classroomName): array
    {
        try {
            $username = config('services.ippanel.username');
            $password = config('services.ippanel.password');
            $from = config('services.ippanel.from_number', '+98BANK');
            $pattern_code = config('services.ippanel.attendance_reminder_pattern_code');

            $to = [$phoneNumber];
            $input_data = ['classroom' => $classroomName];

            $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('cURL error in attendance reminder SMS', [
                    'error' => $error,
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرویس پیامک',
                    'error' => $error,
                ];
            }

            $responseData = json_decode($response, true);

            if ($httpCode === 200) {
                Log::info('Attendance reminder SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'classroom' => $classroomName,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'هشدار حضور و غیاب با موفقیت ارسال شد',
                    'data' => $responseData,
                ];
            }

            Log::error('Attendance reminder SMS sending failed', [
                'phone' => $phoneNumber,
                'classroom' => $classroomName,
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال هشدار حضور و غیاب',
                'error' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Attendance reminder SMS service exception', [
                'phone' => $phoneNumber,
                'classroom' => $classroomName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در سرویس ارسال پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * ارسال پیامک پترن اطلاع‌رسانی ثبت مرخصی به مسئول تایید
     */
    public function sendLeaveApprovalNotification(string $phoneNumber, string $masterName): array
    {
        try {
            $username = config('services.ippanel.username');
            $password = config('services.ippanel.password');
            $from = config('services.ippanel.from_number', '+98BANK');
            $pattern_code = config('services.ippanel.leave_approval_pattern_code');

            $to = [$phoneNumber];
            $input_data = ['master' => $masterName];

            $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('cURL error in leave approval SMS', [
                    'error' => $error,
                    'phone' => $phoneNumber,
                    'master' => $masterName,
                ]);

                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرویس پیامک',
                    'error' => $error,
                ];
            }

            $responseData = json_decode($response, true);

            if ($httpCode === 200) {
                Log::info('Leave approval SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'master' => $masterName,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'اعلان تایید مرخصی با موفقیت ارسال شد',
                    'data' => $responseData,
                ];
            }

            Log::error('Leave approval SMS sending failed', [
                'phone' => $phoneNumber,
                'master' => $masterName,
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال اعلان تایید مرخصی',
                'error' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Leave approval SMS service exception', [
                'phone' => $phoneNumber,
                'master' => $masterName,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در سرویس ارسال پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * بررسی موجودی حساب
     */
    public function getCredit(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'AccessKey ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . '/sms/credit');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'خطا در دریافت موجودی',
                'error' => $response->body(),
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در اتصال به سرویس',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * تولید کد تصادفی
     */
    public function generateVerificationCode(int $length = 6): string
    {
        return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
    }

    /**
     * اعتبارسنجی شماره موبایل ایران
     */
    public function validateIranianMobile(string $phoneNumber): bool
    {
        // حذف فاصله‌ها و کاراکترهای اضافی
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // بررسی فرمت شماره موبایل ایران
        return preg_match('/^(\+98|0098|98|0)?9[0-9]{9}$/', $phoneNumber);
    }

    /**
     * نرمال‌سازی شماره موبایل
     */
    public function normalizePhoneNumber(?string $phoneNumber): string
    {
        // بررسی null بودن شماره
        if ($phoneNumber === null || $phoneNumber === '') {
            return '';
        }

        // حذف فاصله‌ها و کاراکترهای اضافی
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // تبدیل به فرمت استاندارد
        if (str_starts_with($phoneNumber, '0098')) {
            $phoneNumber = substr($phoneNumber, 4);
        } elseif (str_starts_with($phoneNumber, '+98')) {
            $phoneNumber = substr($phoneNumber, 3);
        } elseif (str_starts_with($phoneNumber, '98')) {
            $phoneNumber = substr($phoneNumber, 2);
        } elseif (str_starts_with($phoneNumber, '0')) {
            $phoneNumber = substr($phoneNumber, 1);
        }

        return '98' . $phoneNumber;
    }

    /**
     * ارسال پیامک اطلاع‌رسانی مرخصی طلبه
     */
    public function sendStudentLeaveNotification(string $phoneNumber, string $studentName, string $startDate, string $endDate): array
    {
        try {
            $username = config('services.ippanel.username');
            $password = config('services.ippanel.password');
            $from = config('services.ippanel.from_number', '+98BANK');
            // Pattern code for student leave: ymabiqdfjgy4ejw
            $pattern_code = 'ymabiqdfjgy4ejw';

            $to = [$phoneNumber];
            $input_data = [
                'name' => $studentName,
                'date' => $startDate,
                'date2' => $endDate,
            ];

            $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('cURL error in student leave SMS', [
                    'error' => $error,
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرویس پیامک',
                    'error' => $error,
                ];
            }

            // بررسی پاسخ
            $responseData = json_decode($response, true);

            if ($httpCode === 200) {
                Log::info('Student leave SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'پیامک مرخصی با موفقیت ارسال شد',
                    'data' => $responseData,
                ];
            }

            Log::error('Student leave SMS sending failed', [
                'phone' => $phoneNumber,
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک مرخصی',
                'error' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Student leave SMS service exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در سرویس ارسال پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * ارسال پیامک مجوز خروج به طلبه (نوع انتخابی)
     */
    public function sendStudentLeavePermissionNotification(string $phoneNumber, string $studentName, string $typeAndPeriod): array
    {
        try {
            $username = config('services.ippanel.username');
            $password = config('services.ippanel.password');
            $from = config('services.ippanel.from_number', '+98BANK');
            // Pattern code: i03o7mjckeq5k4u
            $pattern_code = 'i03o7mjckeq5k4u';

            $to = [$phoneNumber];
            $input_data = [
                'name' => $studentName,
                'type-and-period' => $typeAndPeriod,
            ];

            $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('cURL error in student leave permission SMS', [
                    'error' => $error,
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرویس پیامک',
                    'error' => $error,
                ];
            }

            // بررسی پاسخ
            $responseData = json_decode($response, true);

            if ($httpCode === 200) {
                Log::info('Student leave permission SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'پیامک مجوز خروج با موفقیت ارسال شد',
                    'data' => $responseData,
                ];
            }

            Log::error('Student leave permission SMS sending failed', [
                'phone' => $phoneNumber,
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک مجوز خروج',
                'error' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Student leave permission SMS service exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در سرویس ارسال پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * ارسال پیامک مجوز خروج ساعتی به نگهبان
     */
    public function sendHourlyLeavePermissionNotification(string $phoneNumber, string $studentName, string $startTime, string $endTime): array
    {
        try {
            $username = config('services.ippanel.username');
            $password = config('services.ippanel.password');
            $from = config('services.ippanel.from_number', '+98BANK');
            // Pattern code: 4kg52sg24xlam0y
            $pattern_code = '4kg52sg24xlam0y';

            $to = [$phoneNumber];
            $input_data = [
                'name' => $studentName,
                'time' => $startTime,
                'time2' => $endTime,
            ];

            $url = 'https://ippanel.com/patterns/pattern?username=' . $username . '&password=' . urlencode($password) . "&from=$from&to=" . json_encode($to) . '&input_data=' . urlencode(json_encode($input_data)) . "&pattern_code=$pattern_code";

            $handler = curl_init($url);
            curl_setopt($handler, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($handler, CURLOPT_POSTFIELDS, $input_data);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($handler);
            $httpCode = curl_getinfo($handler, CURLINFO_HTTP_CODE);
            $error = curl_error($handler);
            curl_close($handler);

            if ($error) {
                Log::error('cURL error in hourly leave permission SMS', [
                    'error' => $error,
                    'phone' => $phoneNumber,
                ]);

                return [
                    'success' => false,
                    'message' => 'خطا در اتصال به سرویس پیامک',
                    'error' => $error,
                ];
            }

            // بررسی پاسخ
            $responseData = json_decode($response, true);

            if ($httpCode === 200) {
                Log::info('Hourly leave permission SMS sent successfully', [
                    'phone' => $phoneNumber,
                    'response' => $responseData,
                ]);

                return [
                    'success' => true,
                    'message' => 'پیامک مجوز خروج ساعتی با موفقیت ارسال شد',
                    'data' => $responseData,
                ];
            }

            Log::error('Hourly leave permission SMS sending failed', [
                'phone' => $phoneNumber,
                'http_code' => $httpCode,
                'response' => $responseData,
            ]);

            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک مجوز خروج ساعتی',
                'error' => $responseData,
            ];

        } catch (Exception $e) {
            Log::error('Hourly leave permission SMS service exception', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطا در سرویس ارسال پیامک',
                'error' => $e->getMessage(),
            ];
        }
    }
}
