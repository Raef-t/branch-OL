import '/features/auth/presentation/managers/models/auth_model.dart';

abstract class AuthState {}

class AuthInitialState extends AuthState {}

class AuthLoadingState extends AuthState {}

class AuthSuccessState extends AuthState {
  final AuthModel authModelInCubit;
  AuthSuccessState({required this.authModelInCubit});
}

class AuthFailureState extends AuthState {
  final String errorMessageInCubit;
  AuthFailureState({required this.errorMessageInCubit});
}
