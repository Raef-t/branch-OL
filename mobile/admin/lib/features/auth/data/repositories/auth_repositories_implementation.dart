import 'package:dartz/dartz.dart';
import 'package:dio/dio.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/errors/error_server.dart';
import '/core/errors/failure_error.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/features/auth/data/repositories/auth_repositories.dart';
import '/features/auth/data/services/auth_service.dart';
import '/features/auth/presentation/managers/models/auth_model.dart';

class AuthRepositoriesImplementation implements AuthRepositories {
  final AuthService authService;
  AuthRepositoriesImplementation({required this.authService});
  @override
  Future<Either<FailureError, AuthModel>> login({
    required String uniqueId,
    required String password,
  }) async {
    try {
      final response = await authService.login(
        uniqueId: uniqueId,
        password: password,
      );
      final authModel = AuthModel.fromJson(json: response.data['data']);
      await StoreParametersInSharedPreferences.saveStringParameter(
        stringValue: authModel.token ?? '',
        key: keyTokenAuthToUserInSharedPreferences,
      );
      await StoreParametersInSharedPreferences.saveStringParameter(
        stringValue: authModel.userModel?.name ?? '',
        key: keyUserNameInSharedPreferences,
      );
      await StoreParametersInSharedPreferences.saveStringParameter(
        stringValue: authModel.userModel?.photo ?? '',
        key: keyUserPhotoInSharedPreferences,
      );
      await StoreParametersInSharedPreferences.saveIntParameter(
        intValue: authModel.userModel?.id ?? 1,
        key: keyUserIdInSharedPreferences,
      );
      return Right(authModel);
    } on DioException catch (e) {
      return Left(ErrorServer.fromDioException(dioException: e));
    } catch (e) {
      return Left(
        ErrorServer(
          errorMessageInFailureError:
              'خطأ: التقاط مشكلة غير معروفة من عملية سيرفر تسجيل دخول، المزيد من التفاصيل ${e.toString()}',
        ),
      );
    }
  }
}
