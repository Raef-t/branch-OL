import 'package:flutter_bloc/flutter_bloc.dart';
import '/features/profile/data/repositories/logout/log_out_repositories_implementation.dart';
import '/features/profile/presentation/managers/cubits/logout/log_out_state.dart';

class LogOutCubit extends Cubit<LogOutState> {
  LogOutCubit({required this.logOutRepositoriesImplementation})
    : super(LogOutInitialState());
  final LogOutRepositoriesImplementation logOutRepositoriesImplementation;
  Future<void> logout() async {
    emit(LogOutLoadingState());
    final result = await logOutRepositoriesImplementation.logout();
    result.fold(
      (failure) {
        emit(
          LogOutFailureState(
            errorMessageInCubit: failure.errorMessageInFailureError,
          ),
        );
      },
      (message) {
        emit(LogOutSuccessState(messageInCubit: message));
      },
    );
  }
}
