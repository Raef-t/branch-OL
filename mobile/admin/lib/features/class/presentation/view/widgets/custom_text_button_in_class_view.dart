import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/classes/selection_controller_class.dart';
import '/core/components/text_normal14_component.dart';
import '/core/helpers/build_presence_message_new_helper.dart';
import '/core/styles/colors_style.dart';
import '/features/class/presentation/managers/cubits/batch_students/batch_students_cubit.dart';
import '/features/class/presentation/managers/cubits/batch_students/batch_students_state.dart';
import '/features/class/presentation/managers/cubits/send_m_t_n_message/send_m_t_n_message_cubit.dart';

class CustomTextButtonInClassView extends StatelessWidget {
  const CustomTextButtonInClassView({
    super.key,
    required this.text,
    required this.status,
  });

  final String text;
  final String status;

  @override
  Widget build(BuildContext context) {
    return TextButton(
      onPressed: () => _onPressed(context),
      child: TextNormal14Component(
        text: text,
        color: ColorsStyle.mediumBlackColor2,
      ),
    );
  }

  void _onPressed(BuildContext context) {
    final selectionController = context.read<SelectionControllerClass>();

    if (!selectionController.hasSelection) {
      Navigator.pop(context);
      return;
    }

    final batchState = context.read<BatchStudentsCubit>().state;

    if (batchState is! BatchStudentsSuccessState) {
      Navigator.pop(context);
      return;
    }

    final selectedNames = selectionController.selectedIndexes
        .map((i) => batchState.listOfBatchStudentsModelInCubit[i].fullName)
        .whereType<String>()
        .toList();

    final message = buildPresenceMessage(
      studentNames: selectedNames,
      status: status,
    );

    context.read<SendMTNMessageCubit>().sendSms(
      from: 'Al Olamaa',
      to: ['963984900500'], // ✅ fixed number
      contentMessage: message,
      language: 0, // Arabic
    );

    selectionController.exitSelectionMode();
    Navigator.pop(context);
  }
}
