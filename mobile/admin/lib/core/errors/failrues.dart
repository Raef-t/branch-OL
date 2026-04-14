import 'package:dio/dio.dart';

class ErrorServer {
  final String errorMessage;
  ErrorServer({required this.errorMessage});
}

class Failrues extends ErrorServer {
  Failrues({required super.errorMessage});
  factory Failrues.fromDioException({required DioException dioException}) {
    return switch (dioException.type) {
      DioExceptionType.connectionTimeout => Failrues(
        errorMessage: 'خطأ: وقت الاتصال قد استغرق وقت كبير',
      ),
      DioExceptionType.sendTimeout => Failrues(
        errorMessage: 'خطأ: الإرسال استغرق وقت كبير',
      ),
      DioExceptionType.receiveTimeout => Failrues(
        errorMessage: 'خطأ: الاستقبال استغرق وقت كبير',
      ),
      DioExceptionType.badCertificate => Failrues(
        errorMessage: 'خطأ: شهادة سيئة',
      ),
      DioExceptionType.badResponse => Failrues.fromBadResponse(
        response: dioException.response!.data,
        statusCode: dioException.response!.statusCode!,
      ),
      DioExceptionType.cancel => Failrues(errorMessage: 'خطأ: تم إلغاء الطلب'),
      DioExceptionType.connectionError => Failrues(
        errorMessage: 'خطأ: يرجى التأكد من الشبكة',
      ),
      DioExceptionType.unknown => Failrues(
        errorMessage: 'خطأ: حدث خطأ غير معروف يرجى المحاولة مرة أخرى',
      ),
    };
  }
  factory Failrues.fromBadResponse({
    required dynamic response,
    required int statusCode,
  }) {
    return switch (statusCode) {
      400 => Failrues(errorMessage: 'خطأ: هذا الطلب غير صالح ${response['']}'),
      401 => Failrues(errorMessage: 'خطأ: هذا الطلب غير مصرح ${response['']}'),
      403 => Failrues(errorMessage: 'خطأ: هذا الطلب ممنوع ${response['']}'),
      404 => Failrues(errorMessage: 'خطأ: هذا الطلب غير موجود'),
      422 => Failrues(errorMessage: 'خطأ: خطأ في التحقق من البيانات'),
      429 => Failrues(
        errorMessage: 'خطأ: عدد الطلبات كثير جدا يرجى المحاولة لاحقا',
      ),
      500 => Failrues(
        errorMessage:
            'خطأ: يرجى المحاولة لاحقا لأنه يوجد عطل في الخادم ويتم العمل على إصلاحه',
      ),
      502 => Failrues(errorMessage: 'خطأ: هذه البوابة غير صالحة من الخادم'),
      503 => Failrues(errorMessage: 'خطأ: الخدمة غير متوفرة من الخادم'),
      504 => Failrues(errorMessage: 'خطأ: انتهت مهلة البوابة من الخادم'),
      _ => Failrues(errorMessage: 'خطأ: حدث خطأ غير متوقع يرجى المحاولة لاحقا'),
    };
  }
}
