import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/class/data/repositories/send_m_t_n_message/send_m_t_n_message_repositories_implementation.dart';
import '/features/class/presentation/managers/cubits/send_m_t_n_message/send_m_t_n_message_state.dart';

class SendMTNMessageCubit extends Cubit<SendMTNMessageState> {
  SendMTNMessageCubit({required this.sendMTNMessageRepositoriesImplementation})
    : super(SendMTNMessageInitialState());
  final SendMTNMessageRepositoriesImplementation
  sendMTNMessageRepositoriesImplementation;
  Future<void> sendSms({
    required String from,
    required List<String> to,
    required String contentMessage,
    required int language,
  }) async {
    emit(SendMTNMessageLoadingState());
    final result = await sendMTNMessageRepositoriesImplementation.sendSms(
      from: from,
      to: to,
      contentMessage: contentMessage,
      language: language,
    );
    result.fold(
      (failure) {
        emit(
          SendMTNMessageFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (_) {
        emit(SendMTNMessageSuccessState());
      },
    );
  }
}
