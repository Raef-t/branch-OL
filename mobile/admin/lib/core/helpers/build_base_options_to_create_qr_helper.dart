import 'package:dio/dio.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/constants/string_variables_constant.dart';

BaseOptions buildBaseOptionsToCreateQrHelper() {
  return BaseOptions(
    baseUrl: kBaseUrlToHoleApp,
    connectTimeout: k30Seconds,
    receiveTimeout: k30Seconds,
    sendTimeout: k30Seconds,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-DEVICE-KEY': '123',
    },
  );
}
