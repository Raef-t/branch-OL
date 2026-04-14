import '/features/q_r/presentation/managers/models/door_session_model.dart';

abstract class DoorSessionState {}

class DoorSessionInitialState extends DoorSessionState {}

class DoorSessionLoadingState extends DoorSessionState {}

class DoorSessionSuccessState extends DoorSessionState {
  final DoorSessionModel doorSessionModel;

  DoorSessionSuccessState({required this.doorSessionModel});
}

class DoorSessionFailureState extends DoorSessionState {
  final String errorMessage;

  DoorSessionFailureState({required this.errorMessage});
}
