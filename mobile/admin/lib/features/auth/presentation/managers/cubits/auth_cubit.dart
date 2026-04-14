import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/auth/data/repositories/auth_repositories_implementation.dart';
import '/features/auth/presentation/managers/cubits/auth_state.dart';

class AuthCubit extends Cubit<AuthState> {
  AuthCubit({required this.authRepositoriesImplementation})
    : super(AuthInitialState());
  final AuthRepositoriesImplementation authRepositoriesImplementation;
  Future<void> loginMethod({
    required String uniqueId,
    required String password,
  }) async {
    emit(AuthLoadingState());
    final result = await authRepositoriesImplementation.login(
      uniqueId: uniqueId,
      password: password,
    );
    result.fold(
      (failure) => emit(
        AuthFailureState(
          errorMessageInCubit: failure.errorMessageInFailureError,
        ),
      ),
      (authModel) => emit(AuthSuccessState(authModelInCubit: authModel)),
    );
  }
}
