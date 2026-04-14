import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';

class ScanQrService {
  final Dio dio;
  ScanQrService({required this.dio});
  Future<Response> scanQr({required String qrContent}) async {
    final response = await dio.post(
      kScanQREndPoint,
      data: {'qr_content': qrContent},
    );
    return response;
  }
}
