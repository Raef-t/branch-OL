// ignore_for_file: use_build_context_synchronously
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '/core/border_radius/circulars.dart';
import '/core/components/app_bar_widget_with_right_arrow_image_and_three_texts_component.dart';
import '/core/components/background_body_to_views_component.dart';
import '/core/components/circle_loading_state_component.dart';
import '/core/components/failure_state_component.dart';
import '/core/components/filter_card_and_search_field_component.dart';
import '/core/components/sliver_app_bar_to_hole_app_component.dart';
import '/core/components/text_medium12_component.dart';
import '/core/components/text_medium14_component.dart';
import '/core/components/text_medium16_component.dart';
import '/core/components/text_success_state_but_the_data_is_empty_component.dart';
import '/core/constants/string_variables_constant.dart';
import '/core/helpers/push_go_router_helper.dart';
import '/core/paddings/padding_with_child/only_padding_with_child.dart';
import '/core/paddings/padding_with_child/symmetric_padding_with_child.dart';
import '/core/paddings/padding_without_child/only_padding_without_child.dart';
import '/core/sized_boxs/heights.dart';
import '/core/sized_boxs/widths.dart';
import '/core/store/store_parameters_in_shared_preferences.dart';
import '/core/styles/colors_style.dart';
import '/features/mark_to_batch/presentation/managers/cubits/last_and_current_weeks_exams_to_batch_cubit.dart';
import '/features/mark_to_batch/presentation/managers/cubits/last_and_current_weeks_exams_to_batch_state.dart';
import '/gen/assets.gen.dart';
import '/gen/fonts.gen.dart';

class CustomMarkToBatchViewBody extends StatelessWidget {
  const CustomMarkToBatchViewBody({super.key});

  @override
  Widget build(BuildContext context) {
    return CustomScrollView(
      slivers: [
        const SliverAppBarToHoleAppComponent(
          appBarWidget: AppBarWidgetWithRightArrowImageAndThreeTextsComponent(
            firstText: 'العلامات',
            secondText: 'يمكنك الاطلاع على جميع علامات التابعة',
            thirdText: 'للشعبة',
          ),
        ),
        SliverFillRemaining(
          hasScrollBody: false,
          child: BackgroundBodyToViewsComponent(
            child: Column(
              children: [
                Heights.height34(context: context),
                SymmetricPaddingWithChild.horizontal22(
                  context: context,
                  child: FilterCardAndSearchFieldComponent(
                    imageProvider: Assets.images.blueFilterImage.provider(),
                  ),
                ),
                Heights.height40(context: context),
                OnlyPaddingWithChild.right22(
                  context: context,
                  child: const Align(
                    alignment: Alignment.centerRight,
                    child: TextMedium16Component(
                      text: 'الاسبوع الحالي',
                      fontFamily: FontFamily.tajawal,
                      color: ColorsStyle.mediumBlackColor2,
                    ),
                  ),
                ),
                Heights.height24(context: context),
                BlocBuilder<
                  LastAndCurrentWeeksExamsToBatchCubit,
                  LastAndCurrentWeeksExamsToBatchState
                >(
                  builder: (context, state) {
                    if (state is LastAndCurrentWeeksExamsToBatchSuccessState) {
                      final currentWeekExamsToBatchList = state
                          .currentAndLastWeeksExamsToBatchModelInCubit
                          .listOfCurrentWeekMarks;
                      final length = currentWeekExamsToBatchList?.length ?? 0;
                      if ((currentWeekExamsToBatchList ?? []).isEmpty) {
                        return const TextSuccessStateButTheDataIsEmptyComponent(
                          text: 'لا يوجد',
                        );
                      }
                      return Column(
                        children: List.generate(length, (index) {
                          final currentWeekExamsToBatchModel =
                              currentWeekExamsToBatchList?[index];
                          return GestureDetector(
                            onTap: () async {
                              await StoreParametersInSharedPreferences.saveIntParameter(
                                intValue: currentWeekExamsToBatchModel?.id ?? 0,
                                key: keySubjectToBatchInSharedPreferences,
                              );
                              pushGoRouterHelper(
                                context: context,
                                view: kDetailsMarkToBatchViewRouter,
                              );
                            },
                            child: Card(
                              elevation: 0,
                              margin:
                                  OnlyPaddingWithoutChild.left18AndRight22AndBottom8(
                                    context: context,
                                  ),
                              shape: RoundedRectangleBorder(
                                borderRadius: Circulars.circular10(
                                  context: context,
                                ),
                              ),
                              color: ColorsStyle.whiteColor,
                              child: Directionality(
                                textDirection: TextDirection.rtl,
                                child: ListTile(
                                  title: TextMedium14Component(
                                    text:
                                        currentWeekExamsToBatchModel
                                            ?.subjectName ??
                                        'لا يوجد',
                                    color: ColorsStyle.mediumBlackColor2,
                                  ),
                                  subtitle: Row(
                                    children: [
                                      Assets.images.dateImage.image(),
                                      Widths.width11(context: context),
                                      TextMedium12Component(
                                        text:
                                            currentWeekExamsToBatchModel
                                                ?.date ??
                                            'لا يوجد',
                                        fontFamily: FontFamily.tajawal,
                                        color: ColorsStyle.greyColor,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          );
                        }),
                      );
                    } else if (state
                        is LastAndCurrentWeeksExamsToBatchFailureState) {
                      return FailureStateComponent(
                        errorText: state.errorMessageInCubit,
                      );
                    } else {
                      return const CircleLoadingStateComponent();
                    }
                  },
                ),
                Heights.height12(context: context),
                OnlyPaddingWithChild.right22(
                  context: context,
                  child: const Align(
                    alignment: Alignment.centerRight,
                    child: TextMedium16Component(
                      text: 'الاسبوع الماضي',
                      fontFamily: FontFamily.tajawal,
                      color: ColorsStyle.mediumBlackColor2,
                    ),
                  ),
                ),
                Heights.height24(context: context),
                BlocBuilder<
                  LastAndCurrentWeeksExamsToBatchCubit,
                  LastAndCurrentWeeksExamsToBatchState
                >(
                  builder: (context, state) {
                    if (state is LastAndCurrentWeeksExamsToBatchSuccessState) {
                      final lastWeekExamsToBatchList = state
                          .currentAndLastWeeksExamsToBatchModelInCubit
                          .listOfLastWeekMarks;
                      final length = lastWeekExamsToBatchList?.length ?? 0;
                      if ((lastWeekExamsToBatchList ?? []).isEmpty) {
                        return const TextSuccessStateButTheDataIsEmptyComponent(
                          text: 'لا يوجد',
                        );
                      }
                      return Column(
                        children: List.generate(length, (index) {
                          final lastWeekExamsToBatchModel =
                              lastWeekExamsToBatchList?[index];
                          return GestureDetector(
                            onTap: () async {
                              await StoreParametersInSharedPreferences.saveIntParameter(
                                intValue: lastWeekExamsToBatchModel?.id ?? 0,
                                key: keySubjectToBatchInSharedPreferences,
                              );
                              pushGoRouterHelper(
                                context: context,
                                view: kDetailsMarkToBatchViewRouter,
                              );
                            },
                            child: Card(
                              elevation: 0,
                              margin:
                                  OnlyPaddingWithoutChild.left18AndRight22AndBottom8(
                                    context: context,
                                  ),
                              shape: RoundedRectangleBorder(
                                borderRadius: Circulars.circular10(
                                  context: context,
                                ),
                              ),
                              color: ColorsStyle.whiteColor,
                              child: Directionality(
                                textDirection: TextDirection.rtl,
                                child: ListTile(
                                  title: TextMedium14Component(
                                    text:
                                        lastWeekExamsToBatchModel
                                            ?.subjectName ??
                                        'لا يوجد',
                                    color: ColorsStyle.mediumBlackColor2,
                                  ),
                                  subtitle: Row(
                                    children: [
                                      Assets.images.dateImage.image(),
                                      Widths.width11(context: context),
                                      TextMedium12Component(
                                        text:
                                            lastWeekExamsToBatchModel?.date ??
                                            'لا يوجد',
                                        fontFamily: FontFamily.tajawal,
                                        color: ColorsStyle.greyColor,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ),
                          );
                        }),
                      );
                    } else if (state
                        is LastAndCurrentWeeksExamsToBatchFailureState) {
                      return FailureStateComponent(
                        errorText: state.errorMessageInCubit,
                      );
                    } else {
                      return const CircleLoadingStateComponent();
                    }
                  },
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}
