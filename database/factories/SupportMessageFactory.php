<?php

namespace Database\Factories;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportMessageFactory extends Factory
{
    public function definition(): array
    {
        $customerMessages = [
            'I’m facing an issue with my order, it hasn’t been delivered yet.',
            'Can you help me reset my account password?',
            'I was charged twice for the same transaction.',
            'The app keeps crashing when I log in.',
            'My coupon code is not working during checkout.',
            'I uploaded my documents but verification is still pending.',
            'When will my refund be processed?',
            'The website shows “internal server error” when I pay.',
            'I need urgent support, please respond quickly.',
            'My order status is stuck at “processing” for two days.',
            'I received the wrong product in my delivery.',
            'The package I got is damaged, what should I do?',
            'I entered the wrong shipping address, can I change it?',
            'My payment failed but the amount was deducted.',
            'I can’t log in even after resetting my password.',
            'I want to cancel my order before it gets shipped.',
            'Why is the delivery taking longer than expected?',
            'Do you support international shipping?',
            'I was promised a discount, but it’s not applied.',
            'My account has been locked for suspicious activity.',
            'The tracking ID you provided is not working.',
            'I can’t upload my KYC documents, it shows an error.',
            'The live chat support is not responding.',
            'My order was marked delivered but I haven’t received it.',
            'I want to change my payment method for this order.',
            'The delivery agent was rude, I want to report this.',
            'Do you provide gift wrapping options?',
            'My refund is showing “processed” but not credited yet.',
            'I got a smaller quantity than what I ordered.',
            'How do I update my phone number in the account?',
            'I’m not receiving OTPs for login.',
            'Can I schedule delivery for a later date?',
            'The app is showing wrong prices compared to the website.',
            'I mistakenly ordered twice, can you cancel one?',
            'Do you provide exchange for defective items?',
            'How do I delete my account permanently?',
            'My wallet balance is not showing after recharge.',
            'The system keeps logging me out automatically.',
            'I need an invoice copy for my last purchase.',
            'Do you have a replacement policy for electronics?',
            'My subscription is not activated even after payment.',
            'I need to talk to a human agent, not a bot.',
            'Why is my order split into multiple shipments?',
            'The loyalty points are not added to my account.',
            'I need faster delivery, do you offer express shipping?',
            'I was promised cashback, but I didn’t receive it.',
            'The checkout button is not working in the app.',
            'Do you support payments via PayPal?',
            'How do I update my saved credit card?',
            'I forgot my registered email, how do I recover my account?',
            'The product description doesn’t match what I received.',
            'Why is my order showing “on hold”?',
            'Can I return a product without the original packaging?',
            'Do you provide support in languages other than English?',
            'I’m unable to apply multiple coupons in one order.',
            'The order confirmation email hasn’t arrived yet.',
        ];

        $adminMessages = [
            'We’ve received your request, our support team is looking into it.',
            'Can you please share the transaction ID so we can verify?',
            'We’re sorry for the inconvenience. The issue has been escalated.',
            'Your refund has been initiated, it should reflect in 3–5 days.',
            'We reset your account password, please check your email.',
            'Our team is currently investigating the issue.',
            'Your order has been shipped, tracking ID: #TRX-' . rand(100000, 999999),
            'Please try clearing your app cache and log in again.',
            'This issue has been resolved, kindly confirm on your side.',
            'We’ve forwarded your complaint to the delivery partner.',
            'Your KYC documents are under review, you’ll be notified shortly.',
            'We apologize for the delay, your order will be delivered soon.',
            'Please share a screenshot of the error you’re facing.',
            'We’ve updated your shipping address as requested.',
            'Your subscription has been activated successfully.',
            'You’ll receive cashback within 48 hours.',
            'The replacement request has been approved.',
            'Your account has been unlocked, please try again.',
            'We’ve added loyalty points to your account manually.',
            'A new invoice copy has been sent to your email.',
            'Please allow 24 hours for the refund to reflect.',
            'Your issue has been escalated to the technical team.',
            'We’ve sent a new OTP to your registered phone number.',
            'Your payment has been verified successfully.',
            'We’re arranging a reverse pickup for your return.',
            'You’ll be contacted by our delivery partner shortly.',
            'We’ve applied the discount manually for your order.',
            'The duplicate order has been cancelled successfully.',
            'We’ve extended your delivery timeline by 2 days.',
            'The exchange request is under processing.',
            'We’ve resolved the login issue from our side.',
            'Your account details have been updated.',
            'We’ll escalate this to the product team for review.',
            'The out-of-stock item will be restocked soon.',
            'Please reinstall the app and try again.',
            'Your request has been assigned to a senior agent.',
            'We’ve initiated a ticket with our payment gateway provider.',
            'You’ll be notified once the order reaches your city.',
            'Your wallet balance has been restored.',
            'We’ve processed your cancellation request.',
            'A detailed investigation report will be shared soon.',
            'We’ve added additional security checks to your account.',
            'The courier has been instructed to re-attempt delivery.',
            'Please share the IMEI number for verification.',
            'Your case is now being handled by our escalation team.',
            'We’ve confirmed the refund with your bank.',
            'You’ll receive your invoice in the next 2 hours.',
            'The product is covered under warranty, we’ll replace it.',
            'We’ve scheduled express delivery for your order.',
            'Thank you for your patience, the bug has been fixed.',
            'We’ve requested the bank to speed up the refund process.',
            'The coupon code has been reactivated for your account.',
            'We’ll follow up with you once the update is deployed.',
        ];


        $isCustomer = $this->faker->boolean(80);

        return [
            'ticket_id'   => SupportTicket::factory(),
            'sender_id'   => $isCustomer ? fn() => SupportTicket::factory()->create()->user_id : 1,
            'sender_type' => $isCustomer ? 'customer' : 'admin',
            'message'     => $isCustomer
                ? $this->faker->randomElement($customerMessages)
                : $this->faker->randomElement($adminMessages),
            'attachments' => $this->faker->boolean(10) && !$isCustomer
                ? [$this->faker->imageUrl()]
                : [],
        ];
    }
}

