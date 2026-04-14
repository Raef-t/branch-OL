import '/features/class/presentation/managers/models/batch_students_model.dart';

abstract class BatchStudentsState {}

class BatchStudentsInitialState extends BatchStudentsState {}

class BatchStudentsLoadingState extends BatchStudentsState {}

class BatchStudentsSuccessState extends BatchStudentsState {
  final List<BatchStudentsModel> listOfBatchStudentsModelInCubit;
  BatchStudentsSuccessState({required this.listOfBatchStudentsModelInCubit});
}

class BatchStudentsFailureState extends BatchStudentsState {
  final String errorMessageInCubit;
  BatchStudentsFailureState({required this.errorMessageInCubit});
}
