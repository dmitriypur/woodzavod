<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    /**
     * Store a newly created lead in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Валидация данных
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'message' => 'nullable|string',
            'house_id' => 'nullable|exists:houses,id',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Создание заявки
        $lead = Lead::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'message' => $request->message,
            'house_id' => $request->house_id,
        ]);

        // Редирект с сообщением об успехе
        return back()->with('success', 'Ваша заявка успешно отправлена. Мы свяжемся с вами в ближайшее время.');
    }

    /**
     * Submit form via AJAX with email and telegram notifications
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitForm(Request $request)
    {
        try {
            // Валидация данных
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'email' => 'nullable|email|max:255',
                'message' => 'nullable|string|max:1000',
                'house_id' => 'nullable|exists:houses,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка валидации: ' . $validator->errors()->first()
                ], 422);
            }

            // Создание заявки
            $lead = Lead::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'message' => $request->message,
                'house_id' => $request->house_id,
            ]);

            // Отправка email уведомления
            $this->sendEmailNotification($lead);

            // Отправка в Telegram
            $this->sendTelegramNotification($lead);

            return response()->json([
                'success' => true,
                'message' => 'Заявка успешно отправлена! Мы свяжемся с вами в ближайшее время.'
            ]);

        } catch (\Exception $e) {
            Log::error('Form submission error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Произошла ошибка при отправке заявки. Попробуйте позже.'
            ], 500);
        }
    }

    /**
     * Send email notification
     *
     * @param Lead $lead
     * @return void
     */
    private function sendEmailNotification(Lead $lead)
    {
        try {
            $adminEmail = env('ADMIN_EMAIL', 'admin@woodzavod.ru');
            
            Mail::send('emails.new-lead', ['lead' => $lead], function ($message) use ($adminEmail, $lead) {
                $message->to($adminEmail)
                        ->subject('Новая заявка с сайта WoodZavod')
                        ->from(env('MAIL_FROM_ADDRESS', 'noreply@woodzavod.ru'), env('MAIL_FROM_NAME', 'WoodZavod'));
            });
        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage());
        }
    }

    /**
     * Send telegram notification
     *
     * @param Lead $lead
     * @return void
     */
    private function sendTelegramNotification(Lead $lead)
    {
        try {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            $chatId = env('TELEGRAM_CHAT_ID');
            
            if (!$botToken || !$chatId) {
                Log::warning('Telegram credentials not configured');
                return;
            }

            $houseName = $lead->house ? $lead->house->title : 'Не указан';
            
            $message = "🏠 *Новая заявка с сайта \"Деревянное домостроение\"*\n\n";
            $message .= "👤 *Имя:* {$lead->name}\n";
            $message .= "📞 *Телефон:* {$lead->phone}\n";
            if ($lead->email) {
                $message .= "📧 *Email:* {$lead->email}\n";
            }
            $message .= "🏡 *Дом:* {$houseName}\n";
            if ($lead->message) {
                $message .= "💬 *Сообщение:* {$lead->message}\n";
            }
            $message .= "⏰ *Время:* " . $lead->created_at->format('d.m.Y H:i');

            Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);
        } catch (\Exception $e) {
            Log::error('Telegram sending error: ' . $e->getMessage());
        }
    }
}