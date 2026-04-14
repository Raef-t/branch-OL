import 'package:dio/dio.dart';
import '/core/errors/failure_error.dart';

class ErrorServer extends FailureError {
  ErrorServer({required super.errorMessageInFailureError});
  factory ErrorServer.fromDioException({required DioException dioException}) {
    return switch (dioException.type) {
      DioExceptionType.connectionTimeout => ErrorServer(
        errorMessageInFailureError: 'خطأ: الاتصال استغرق وقت طويل',
      ),
      DioExceptionType.sendTimeout => ErrorServer(
        errorMessageInFailureError: 'خطأ: الإرسال استغرق وقت طويل',
      ),
      DioExceptionType.receiveTimeout => ErrorServer(
        errorMessageInFailureError: 'خطأ: الاستقبال استغرق وقت طويل',
      ),
      DioExceptionType.badCertificate => ErrorServer(
        errorMessageInFailureError:
            'خطأ: تعذر الاتصال بالخادم بسبب مشكلة في امان الاتصال يرجى المحاولة لاحقا',
      ),
      DioExceptionType.badResponse => ErrorServer.fromBadResponse(
        response: dioException.response?.data ?? {},
        statusCode: dioException.response?.statusCode ?? 0,
      ),
      DioExceptionType.cancel => ErrorServer(
        errorMessageInFailureError: 'خطأ: تم إلغاء الطلب قبل اكتمال العملية',
      ),
      DioExceptionType.connectionError => ErrorServer(
        errorMessageInFailureError: 'خطأ: يرجى التحقق من الاتصال في الانترنت',
      ),
      DioExceptionType.unknown => ErrorServer(
        errorMessageInFailureError:
            'خطأ: حدثت مشكلة غير متوقعه يرجى المحاولة مرة أخرى',
      ),
    };
  }
  factory ErrorServer.fromBadResponse({
    required dynamic response,
    required int statusCode,
  }) {
    String? message;
    if (response is Map<String, dynamic>) {
      message = response['message'];
    }

    return switch (statusCode) {
      400 => ErrorServer(
        errorMessageInFailureError: 'خطأ: ${message ?? 'طلب غير صالح'}',
      ),
      401 => ErrorServer(
        errorMessageInFailureError: 'خطأ: ${message ?? 'بيانات الاعتماد غير صحيحة'}',
      ),
      403 => ErrorServer(
        errorMessageInFailureError: 'خطأ: ${message ?? 'الوصول ممنوع'}',
      ),
      404 => ErrorServer(
        errorMessageInFailureError: 'خطأ: ${message ?? 'المورد غير موجود'}',
      ),
      422 => ErrorServer(
        errorMessageInFailureError: 'خطأ: ${message ?? 'البيانات المدخلة غير صحيحة'}',
      ),
      429 => ErrorServer(
        errorMessageInFailureError:
            'خطأ: عدد كبير من الطلبات يرجى المحاولة بعد قليل',
      ),
      500 => ErrorServer(
        errorMessageInFailureError:
            'خطأ: توجد مشكلة في السيرفر و يتم العمل على حلها يرجى المحاولة في وقت آخر',
      ),
      502 => ErrorServer(
        errorMessageInFailureError: 'خطأ: حدثت مشكلة في الاتصال بالخادم الوسيط',
      ),
      503 => ErrorServer(
        errorMessageInFailureError:
            'خطأ: الخدمة غير متاحة حاليا يرجى المحاولة لاحقا',
      ),
      _ => ErrorServer(
        errorMessageInFailureError:
            'خطأ: حدثت مشكلة غير متوقعه يرجى المحاولة في وقت آخر',
      ),
    };
  }
}
