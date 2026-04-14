import 'dart:async';

import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/constants/duration_variables_constant.dart';
import '/features/q_r/data/repositories/door_session_repositories_implementation.dart';
import '/features/q_r/presentation/managers/cubits/door_session_state.dart';

class DoorSessionCubit extends Cubit<DoorSessionState> {
  DoorSessionCubit({required this.doorSessionRepositoryImplementation})
    : super(DoorSessionInitialState());

  final DoorSessionRepositoriesImplementation
  doorSessionRepositoryImplementation;
  Timer? timer;
  void autoGenerateSession() {
    if (isClosed) return;
    //if the cubit is closed so don't run anything in this method and getout from it
    emit(DoorSessionLoadingState());
    generateDoorSession();
    //first open this method(when i go to QRView) should qr immediately created
    // then every 3 seconds should qr creating(but when, when the QRView is opening now)
    timer?.cancel();
    timer = Timer.periodic(k3Seconds, (_) => generateDoorSession());
  }

  Future<void> generateDoorSession() async {
    final result = await doorSessionRepositoryImplementation
        .generateDoorSession(deviceId: 'DOOR_MAIN_01');
    if (isClosed) return;
    //if the cubit is closed so getout from it
    result.fold(
      (failure) {
        emit(
          DoorSessionFailureState(
            errorMessage: failure.errorMessageInFailureError,
          ),
        );
      },
      (model) {
        emit(DoorSessionSuccessState(doorSessionModel: model));
      },
    );
  }

  @override
  Future<void> close() {
    timer?.cancel();
    return super.close();
  }

  //if i getout from view so the cubit will close, so also close the timer
}
