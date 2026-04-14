import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class SendMTNMessageService {
  final Dio dio;
  SendMTNMessageService({required this.dio});
  Future<Response> sendSms({
    required String from,
    required String to,
    required String hexMessage,
    required int language,
  }) async {
    final response = await dio.get(
      kSendSmsMTNEndPoint,
      queryParameters: {
        'User': 'olmlmrr802',
        'Pass': 'olaasd181012',
        'From': 'Al Olamaa',
        'Gsm': to,
        'Msg': hexMessage,
        'Lang': 0,
      },
    );
    return response;
  }
}
