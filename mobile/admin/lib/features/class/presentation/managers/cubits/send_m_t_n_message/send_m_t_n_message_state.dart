abstract class SendMTNMessageState {}

class SendMTNMessageInitialState extends SendMTNMessageState {}

class SendMTNMessageLoadingState extends SendMTNMessageState {}

class SendMTNMessageSuccessState extends SendMTNMessageState {}

class SendMTNMessageFailureState extends SendMTNMessageState {
  final String errorMessageInCubit;
  SendMTNMessageFailureState({required this.errorMessageInCubit});
}
