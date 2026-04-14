import '/features/teachers/presentation/managers/models/teachers_model.dart';

abstract class TeachersState {}

class TeachersInitialState extends TeachersState {}

class TeachersLoadingState extends TeachersState {}

class TeachersSuccessState extends TeachersState {
  final List<TeachersModel> teachersListInCubit;
  TeachersSuccessState({required this.teachersListInCubit});
}

class TeachersFailureState extends TeachersState {
  final String errorMessageInCubit;
  TeachersFailureState({required this.errorMessageInCubit});
}
