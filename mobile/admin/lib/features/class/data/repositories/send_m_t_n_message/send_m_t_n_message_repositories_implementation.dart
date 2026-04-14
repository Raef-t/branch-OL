import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/helpers/convert_string_to_unicode_hex_helper.dart';
import '/features/class/data/repositories/send_m_t_n_message/send_m_t_n_message_repositories.dart';
import '/features/class/data/services/send_m_t_n_message/send_m_t_n_message_service.dart';

class SendMTNMessageRepositoriesImplementation
    implements SendMTNMessageRepositories {
  final SendMTNMessageService sendMTNMessageService;
  SendMTNMessageRepositoriesImplementation({
    required this.sendMTNMessageService,
  });
  @override
  Future<Either<FailureError, bool>> sendSms({
    required String from,
    required List<String> to,
    required String contentMessage,
    required int language,
  }) async {
    try {
      final String numbers = to.join(';');
      //this shape MTN service accepted it: numbers will take all elements in one single cotation and between all elments will join between them in ;
      final hexMessage = convertStringToUnicodeHexHelper(
        contentMessage: contentMessage,
      );
      final response = await sendMTNMessageService.sendSms(
        from: from,
        to: numbers,
        hexMessage: hexMessage,
        language: language,
      );
      if (response.statusCode == 200) {
        return const Right(true);
      } else {
        print('-------------------------');
        print(response.data);
        print('-------------------------');
        return Left(
          ErrorServer(errorMessageInFailureError: 'فشل إرسال الرسالة'),
        );
      }
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } on Exception catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر إرسال رسالة ام تي ان، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
