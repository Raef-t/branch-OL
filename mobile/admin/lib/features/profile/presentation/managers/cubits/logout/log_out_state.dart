abstract class LogOutState {}

class LogOutInitialState extends LogOutState {}

class LogOutLoadingState extends LogOutState {}

class LogOutSuccessState extends LogOutState {
  final String messageInCubit;
  LogOutSuccessState({required this.messageInCubit});
}

class LogOutFailureState extends LogOutState {
  final String errorMessageInCubit;
  LogOutFailureState({required this.errorMessageInCubit});
}
