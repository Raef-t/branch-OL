import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class DoorSessionService {
  final Dio dio;
  DoorSessionService({required this.dio});
  Future<Response> generateDoorSession({required String deviceId}) async {
    final response = await dio.post(
      kCreateQREndPoint,
      data: {'device_id': deviceId},
    );
    return response;
  }
}
