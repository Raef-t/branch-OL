import 'package:dio/dio.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/constants/string_variables_constant.dart';

BaseOptions buildBaseOptionsToMTNHelper() {
  return BaseOptions(
    baseUrl: kMTNBaseUrl,
    connectTimeout: k30Seconds,
    receiveTimeout: k30Seconds,
    sendTimeout: k30Seconds,
  );
}
