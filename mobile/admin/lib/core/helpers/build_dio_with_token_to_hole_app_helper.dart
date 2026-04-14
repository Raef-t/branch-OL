import 'package:dio/dio.dart';
import '/core/constants/duration_variables_constant.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';

Dio buildDioWithTokenToHoleAppHelper() {
  final dio = Dio(
    BaseOptions(
      baseUrl: kBaseUrlToHoleApp,
      connectTimeout: k30Seconds,
      receiveTimeout: k30Seconds,
      sendTimeout: k30Seconds,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
    ),
  );
  dio.interceptors.add(
    InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token =
            await StoreParametersInSharedPreferences.getStringParameter(
              key: keyTokenAuthToUserInSharedPreferences,
            );
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
    ),
  );
  return dio;
}
